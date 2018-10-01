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
namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use Magento\Sales\Api\OrderRepositoryInterface;

class ProductOptions
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var GuaranteedOptions
     */
    private $guaranteedOptions;

    /**
     * @var ProductOptionsConfiguration
     */
    private $productOptionsConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * These shipment types need specific product options.
     *
     * @var array
     */
    private $availableProductOptions = [
            'pge'     => [
                'Characteristic' => '118',
                'Option'         => '002',
            ],
            'evening' => [
                'Characteristic' => '118',
                'Option'         => '006',
            ],
            'sunday'  => [
                'Characteristic' => '101',
                'Option'         => '008',
            ],
            'idcheck' => [
                'Characteristic' => '002',
                'Option'         => '014'
            ],
            'idcheck_pg' => [
                'Characteristic' => '002',
                'Option'         => '014'
            ],
        ];

    /**
     * ProductOptions constructor.
     *
     * @param ShippingOptions              $shippingOptions
     * @param GuaranteedOptions            $guaranteedOptions
     * @param ProductOptionsConfiguration  $productOptions
     * @param OrderRepositoryInterface     $orderRepository
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        GuaranteedOptions $guaranteedOptions,
        ProductOptionsConfiguration $productOptions,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->shippingOptions      = $shippingOptions;
        $this->guaranteedOptions    = $guaranteedOptions;
        $this->productOptionsConfig = $productOptions;
        $this->orderRepository      = $orderRepository;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param bool   $flat
     *
     * @return null
     */
    public function get($shipment, $flat = false)
    {
        $type = strtolower($shipment->getShipmentType());
        if ($shipment->isIDCheck()) {
            $type = 'idcheck';
        }

        if (!array_key_exists($type, $this->availableProductOptions)) {
            return $this->checkGuaranteedOptions($shipment);
        }

        if ($flat) {
            return $this->availableProductOptions[$type];
        }

        return ['ProductOption' => $this->availableProductOptions[$type]];
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return null|array
     */
    private function checkGuaranteedOptions($shipment)
    {
        if (!$this->shippingOptions->isGuaranteedDeliveryActive()) {
            return null;
        }

        $order = $this->orderRepository->get($shipment->getOrderId());
        $guaranteedTime = $this->productOptionsConfig->getGuaranteedDeliveryType(
            $this->isAlternative($order->getBaseGrandTotal()),
            $this->productOptionsConfig->getGuaranteedType($shipment->getProductCode())
        );

        return $this->guaranteedOptions->get($guaranteedTime);
    }

    /**
     * @param $totalAmount
     *
     * @return bool
     */
    private function isAlternative($totalAmount)
    {
        $alternativeActive = $this->productOptionsConfig->getUseAlternativeDefault();
        if (!$alternativeActive) {
            return false;
        }

        $alternativeMinAmount = $this->productOptionsConfig->getAlternativeDefaultMinAmount();
        if ($totalAmount >= $alternativeMinAmount) {
            return false;
        }

        return true;

    }

}
