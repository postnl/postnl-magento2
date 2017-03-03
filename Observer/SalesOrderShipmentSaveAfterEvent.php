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
namespace TIG\PostNL\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use Magento\Framework\App\RequestInterface;
use TIG\PostNL\Config\Provider\ProductOptions;
use \TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Model\ShipmentFactory;

class SalesOrderShipmentSaveAfterEvent implements ObserverInterface
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Handlers\BarcodeHandler
     */
    private $barcodeHandler;

    /**
     * @var Handlers\SentDateHandler
     */
    private $sentDateHandler;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * Request params
     * @var array
     */
    private $shipParams = [];

    /**
     * @param ShipmentFactory          $shipmentFactory
     * @param OrderRepository          $orderRepository
     * @param Handlers\BarcodeHandler  $barcodeHandler
     * @param Handlers\SentDateHandler $sendDateHandler
     * @param ProductOptions           $productOptions
     * @param RequestInterface         $requestInterface
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        OrderRepository $orderRepository,
        Handlers\BarcodeHandler $barcodeHandler,
        Handlers\SentDateHandler $sendDateHandler,
        ProductOptions $productOptions,
        RequestInterface $requestInterface
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->orderRepository = $orderRepository;
        $this->barcodeHandler = $barcodeHandler;
        $this->sentDateHandler = $sendDateHandler;
        $this->productOptions = $productOptions;

        $this->shipParams = $requestInterface->getParam('shipment');
    }

    /**
     * @codingStandardsIgnoreLine
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getData('data_object');

        /** @var \TIG\PostNL\Model\Shipment $model */
        $model       = $this->shipmentFactory->create();
        $sentDate    = $this->sentDateHandler->get($shipment);
        $mainBarcode = $this->barcodeHandler->generate();

        $model->setData([
            'ship_at' => $sentDate,
            'main_barcode' => $mainBarcode,
            'shipment_id' => $shipment->getId(),
            'order_id' => $shipment->getOrderId(),
            'product_code' => $this->getProductCode($shipment),
        ]);

        $model->setData($this->formatModelData($shipment));
        $model->save();
        $this->handleMultipleParcels($model);
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return mixed
     */
    private function getProductCode($shipment)
    {
        /** @var PostNLOrder $postNLOrder */
        $postNLOrder  = $this->orderRepository->getByFieldWithValue('order_id', $shipment->getOrderId());

        $productCode = $this->productOptions->getDefaultProductOption();
        if ($postNLOrder->getIsPakjegemak()) {
            $productCode = $this->productOptions->getDefaultPakjeGemakProductOption();
        }

        $postNLOrder->setData('product_code', $productCode);
        $this->orderRepository->save($postNLOrder);

        return $productCode;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     *
     * @return array
     */
    private function formatModelData($shipment)
    {
        $sentDate    = $this->sentDateHandler->get($shipment);
        $mainBarcode = $this->barcodeHandler->generate();

        $colliAmount = isset($this->shipParams['tig_postnl_colli_amount'])
            ? $this->shipParams['tig_postnl_colli_amount'] : 1;
        $productCode = isset($this->shipParams['tig_postnl_product_code'])
            ? $this->shipParams['tig_postnl_product_code'] : $this->getProductCode($shipment);

        return [
            'ship_at'      => $sentDate,
            'shipment_id'  => $shipment->getId(),
            'order_id'     => $shipment->getOrderId(),
            'main_barcode' => $mainBarcode,
            'product_code' => $productCode,
            'parcel_count' => $colliAmount
        ];
    }

    /**
     * @param \TIG\PostNL\Model\Shipment $model
     */
    private function handleMultipleParcels($model)
    {
        $parcelCount = $model->getParcelCount();
        if ($parcelCount > 1) {
            $this->barcodeHandler->saveShipment($model->getEntityId(), $parcelCount);
        }
    }
}
