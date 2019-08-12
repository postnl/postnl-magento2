<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Provider\ShippingOptions;

class Validator
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var array
     */
    private $notifications = ['errors' => [], 'notices' => []];

    /**
     * @var bool
     */
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
     * @return array
     */
    public function getErrors()
    {
        return $this->notifications['errors'];
    }

    /**
     * @return array
     */
    public function getNotices()
    {
        return $this->notifications['notices'];
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

        return $this->validatePriority($shipment);
    }

    /**
     * @param $shipment
     *
     * @return bool
     */
    private function validateGlobalPack(ShipmentInterface $shipment)
    {
        if (!$this->shippingOptions->canUseGlobalPack()) {
            $magentoShipment = $shipment->getShipment();
            // @codingStandardsIgnoreLine
            $this->notifications['errors'][] = __('Could not print labels for shipment %1. Worldwide (Globalpack) Delivery is disabled. Please contact your PostNL account manager before you enable this method.', $magentoShipment->getIncrementId());
            return false;
        }

        return $this->validatePriority($shipment);
    }

    /**
     * @param $shipment
     *
     * @return bool
     */
    private function validatePriority(ShipmentInterface $shipment)
    {
        $code = $shipment->getProductCode();
        $isPriority = $this->productOptions->checkProductByFlags($code, 'group', 'priority_options');

        if ($isPriority && !$this->shippingOptions->canUsePriority()) {
            $magentoShipment = $shipment->getShipment();
            // @codingStandardsIgnoreLine
            $this->notifications['errors'][] = __('Could not print labels for shipment %1. Priority Delivery is disabled. Please contact your PostNL account manager before you enable this method.', $magentoShipment->getIncrementId());
            return false;
        }

        /** We want to show this notification for every Priority Shipment */
        if ($isPriority && $this->priorityNotice == false) {
            // @codingStandardsIgnoreLine
            $this->notifications['notices'][] = __('Packet Tracked is a small parcel with Track & Trace. The minimum amount is 5 items. Hand over your Packet Tracked items in a domestic mailbag with a Packet Tracked baglabel attached.');
            $this->priorityNotice = true;
        }

        return true;
    }
}
