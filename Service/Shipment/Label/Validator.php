<?php

namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Provider\ShippingOptions;

class Validator
{
    /** @var ProductOptions */
    private $productOptions;

    /** @var ShippingOptions */
    private $shippingOptions;

    /** @var array */
    private $messages = ['errors' => [], 'notices' => []];

    /** @var bool */
    private $priorityNotice = false;

    /**
     * Validator constructor.
     *
     * @param ProductOptions  $productOptions
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ProductOptions $productOptions,
        ShippingOptions $shippingOptions
    ) {
        $this->productOptions = $productOptions;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * Removes all labels that are empty or not a string. If a shipment has no valid labels, the shipment will be
     * removed from the stack.
     *
     * @param $input
     * @return array
     */
    public function validate($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $filtered = array_filter($input, [$this, 'filterInput']);

        return array_values($filtered);
    }

    /**
     * @param ShipmentLabelInterface|null $model
     *
     * @return bool
     */
    private function filterInput(ShipmentLabelInterface $model = null)
    {
        if ($model === null) {
            return false;
        }

        $label = $model->getLabel();

        if (!is_string($label) || empty($label)) {
            return false;
        }

        $start = substr($label, 0, strlen('invalid'));
        $start = strtolower($start);

        return $start != 'invalid';
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->messages['errors'];
    }

    /**
     * @return mixed
     */
    public function getNotices()
    {
        return $this->messages['notices'];
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function validateProduct(ShipmentInterface $shipment)
    {
        if ($shipment->isGlobalPack()) {
            return $this->validateGlobalPack($shipment);
        }

        if ($shipment->isBuspakjeShipment()) {
            $this->showLetterboxPackageNotice();
        }

        return $this->validatePeps($shipment);
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    private function validateGlobalPack(ShipmentInterface $shipment)
    {
        if (!$this->shippingOptions->canUseGlobalPack()) {
            $magentoShipment = $shipment->getShipment();
            // @codingStandardsIgnoreLine
            $this->messages['errors'][] = __('Could not print labels for shipment %1. Worldwide (Globalpack) Delivery is disabled. Please contact your PostNL account manager before you enable this method.', $magentoShipment->getIncrementId());
            return false;
        }

        return $this->validatePeps($shipment);
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    private function validatePeps(ShipmentInterface $shipment)
    {
        $code = $shipment->getProductCode();
        $isPriority = $this->productOptions->checkProductByFlags($code, 'group', 'priority_options');

        if ($isPriority && !$this->shippingOptions->canUsePriority()) {
            $magentoShipment = $shipment->getShipment();
            // @codingStandardsIgnoreLine
            $this->messages['errors'][] = __('Could not print labels for shipment %1. Priority Delivery is disabled. Please contact your PostNL account manager before you enable this method.', $magentoShipment->getIncrementId());
            return false;
        }

        return true;
    }

    private function showLetterboxPackageNotice()
    {
        // @codingStandardsIgnoreStart
        $this->messages['notices'][] = __('Please note, a letterbox package may not exceed 38 x 26.5 x 3.2 cm
        (14.96 x 10.43 x 1.25 in) in size and 2 kg (4.4 lbs) in weight.');
        // @codingStandardsIgnoreEnd
    }
}
