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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Api\ShipmentManagement;

use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepository;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Handler\SentDateHandler;

class CreateShipment
{
    /** @var Webshop */
    private $webshopConfig;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var MagentoShipmentRepository */
    private $magentoShipmentRepository;

    /** @var OrderRepository */
    private $orderRepository;

    /** @var SentDateHandler */
    private $sentDateHandler;

    public function __construct(
        Webshop $webshopConfig,
        MagentoShipmentRepository $magentoShipmentRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepository $orderRepository,
        SentDateHandler $sentDateHandler
    ) {
        $this->webshopConfig = $webshopConfig;
        $this->shipmentRepository = $shipmentRepository;
        $this->magentoShipmentRepository = $magentoShipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->sentDateHandler = $sentDateHandler;
    }

    /**
     * @param int      $shipmentId
     * @param int|null $productCode
     * @param int|null $colliAmount
     *
     * @return bool
     */
    public function create($shipmentId, $productCode = null, $colliAmount = null)
    {
        if (!$this->validate($shipmentId)) {
            return false;
        }

        $shipmentData = $this->getShipmentData($shipmentId, $productCode, $colliAmount);

        /** @var \TIG\PostNL\Model\Shipment $newShipment */
        $newShipment = $this->shipmentRepository->create();
        $newShipment->setData($shipmentData);
        $this->shipmentRepository->save($newShipment);

        return true;
    }

    /**
     * @param int $shipmentId
     *
     * @return bool
     */
    private function validate($shipmentId)
    {
        if ($this->shipmentRepository->getByShipmentId($shipmentId)) {
            return false;
        }

        /** @var Shipment $magentoShipment */
        $magentoShipment = $this->magentoShipmentRepository->get($shipmentId);
        $order = $magentoShipment->getOrder();
        $shippingMethod = $order->getShippingMethod();
        $allowedMethods = $this->webshopConfig->getAllowedShippingMethods($order->getStoreId());

        if (in_array($shippingMethod, $allowedMethods)) {
            return true;
        }

        return $shippingMethod == 'tig_postnl_regular';
    }

    /**
     * @param int      $shipmentId
     * @param int|null $productCode
     * @param int|null $colliAmount
     *
     * @return array
     */
    private function getShipmentData($shipmentId, $productCode = null, $colliAmount = null)
    {
        /** @var Shipment $magentoShipment */
        $magentoShipment = $this->magentoShipmentRepository->get($shipmentId);
        $sentDate = $this->sentDateHandler->get($magentoShipment);

        /** @var Order $postNLOrder */
        $postNLOrder = $this->orderRepository->getByFieldWithValue('order_id', $magentoShipment->getOrderId());
        $productCode = (($productCode != null && $productCode != 0) ? $productCode : $postNLOrder->getProductCode());
        $colliAmount = (($colliAmount != null && $colliAmount != 0) ? $colliAmount : $postNLOrder->getParcelCount());

        return [
            'ship_at'           => $sentDate,
            'shipment_id'       => $magentoShipment->getId(),
            'order_id'          => $magentoShipment->getOrderId(),
            'product_code'      => $productCode,
            'shipment_type'     => $postNLOrder->getType(),
            'ac_characteristic' => $postNLOrder->getAcCharacteristic(),
            'ac_option'         => $postNLOrder->getAcOption(),
            'ac_information'    => $postNLOrder->getData('ac_information'),
            'parcel_count'      => $colliAmount
        ];
    }
}
