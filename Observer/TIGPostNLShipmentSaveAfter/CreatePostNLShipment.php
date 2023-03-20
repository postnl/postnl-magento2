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
namespace TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Service\Handler\SentDateHandler;
use TIG\PostNL\Config\Provider\Webshop;

// @codingStandardsIgnoreFile
/**
 * $shipmentOrderId is marked as object instance property, don't know why but that's why CodeSniffer is failing.
 */
class CreatePostNLShipment implements ObserverInterface
{
    /**
     * @var null
     */
    private $shipmentOrderId = null;

    /**
     * Request params
     * @var array
     */
    private $shipParams = [];

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SentDateHandler
     */
    private $sentDateHandler;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var PostNLOrder
     */
    private $order;

    /**
     * @var Webshop
     */
    private $webshopConfig;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepository             $orderRepository
     * @param SentDateHandler             $sendDateHandler
     * @param RequestInterface            $requestInterface
     * @param Webshop                     $webshopConfig
     * @param SerializerInterface         $serializer
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepository $orderRepository,
        SentDateHandler $sendDateHandler,
        RequestInterface $requestInterface,
        Webshop $webshopConfig,
        SerializerInterface $serializer
    ) {
        $this->orderRepository = $orderRepository;
        $this->sentDateHandler = $sendDateHandler;
        $this->shipmentRepository = $shipmentRepository;
        $this->webshopConfig = $webshopConfig;
        $this->serializer = $serializer;

        $this->shipParams = $requestInterface->getParam('shipment');
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getData('data_object');

        if (!$this->isPostNLShipment($shipment)) {
            return;
        }

        if ($this->shipmentRepository->getByShipmentId($shipment->getId())) {
            return;
        }

        /** @var \TIG\PostNL\Model\Shipment $model */
        $model = $this->shipmentRepository->create();

        $this->shipmentOrderId = $shipment->getOrderId();
        $model->setData($this->formatModelData($shipment));
        $this->shipmentRepository->save($model);
    }

    /**
     * @return mixed
     */
    private function getProductCode()
    {
        /** @var PostNLOrder $postNLOrder */
        $postNLOrder = $this->getOrder();

        return $postNLOrder->getProductCode();
    }

    /**
     * @return mixed
     */
    private function getParcelCount()
    {
        $postNLOrder = $this->getOrder();

        return $postNLOrder->getParcelCount();
    }

    /**
     * @param Shipment $shipment
     *
     * @return array
     */
    private function formatModelData($shipment)
    {
        $sentDate     = $this->sentDateHandler->get($shipment);
        $shipmentType = $this->getShipmentType();

        $colliAmount = isset($this->shipParams['tig_postnl_colli_amount'])
            ? $this->shipParams['tig_postnl_colli_amount'] : $this->getParcelCount();
        $productCode = isset($this->shipParams['tig_postnl_product_code'])
            ? $this->shipParams['tig_postnl_product_code'] : $this->getProductCode();

        return [
            'ship_at'           => $sentDate,
            'shipment_id'       => $shipment->getId(),
            'order_id'          => $shipment->getOrderId(),
            'product_code'      => $productCode,
            'shipment_type'     => $shipmentType,
            'ac_characteristic' => $this->getAcCharacteristic(),
            'ac_option'         => $this->getAcOption(),
            'ac_information'    => $this->getAcInformation(),
            'parcel_count'      => $colliAmount,
            'insured_tier'      => $this->getInsuredTier()
        ];
    }

    /**
     * @return null|PostNLOrder
     */
    private function getOrder()
    {
        if ($this->order === null || $this->order->getOrderId() != $this->shipmentOrderId) {
            $this->order = $this->orderRepository->getByFieldWithValue('order_id', $this->shipmentOrderId);
        }

        return $this->order;
    }

    /**
     * @return string|null
     */
    private function getShipmentType()
    {
        $order = $this->getOrder();

        return $order->getType();
    }

    /**
     * @return string
     */
    private function getAcCharacteristic()
    {
        $order = $this->getOrder();

        return $order->getAcCharacteristic();
    }

    /**
     * @return null|string
     */
    private function getAcOption()
    {
        $order = $this->getOrder();

        return $order->getAcOption();
    }

    /**
     * @return null|string
     */
    private function getInsuredTier()
    {
        $order = $this->getOrder();

        return $order->getInsuredTier();
    }

    /**
     * @return null|string
     */
    private function getAcInformation()
    {
        $order = $this->getOrder();
        $acInformation = $order->getAcInformation();

        return $this->serializer->serialize($acInformation);
    }

    /**
     * @param Shipment $shipment
     *
     * @return bool
     */
    private function isPostNLShipment(Shipment $shipment)
    {
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod();

        $allowedMethods = $this->webshopConfig->getAllowedShippingMethods($order->getStoreId());
        if (in_array($shippingMethod, $allowedMethods)) {
            return true;
        }

        return $shippingMethod == 'tig_postnl_regular';
    }
}
