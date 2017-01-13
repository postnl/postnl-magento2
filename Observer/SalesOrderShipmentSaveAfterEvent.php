<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\ShipmentFactory;

class SalesOrderShipmentSaveAfterEvent implements ObserverInterface
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

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
     * @param ShipmentFactory          $shipmentFactory
     * @param OrderFactory             $orderFactory
     * @param Handlers\BarcodeHandler  $barcodeHandler
     * @param Handlers\SentDateHandler $sendDateHandler
     * @param ProductOptions           $productOptions
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        OrderFactory $orderFactory,
        Handlers\BarcodeHandler $barcodeHandler,
        Handlers\SentDateHandler $sendDateHandler,
        ProductOptions $productOptions
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->orderFactory = $orderFactory;
        $this->barcodeHandler = $barcodeHandler;
        $this->sentDateHandler = $sendDateHandler;
        $this->productOptions = $productOptions;
    }

    /**
     * @codingStandardsIgnoreLine
     * @TODO: actually get & save the parcel count
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
        $model = $this->shipmentFactory->create();

        $sentDate = $this->sentDateHandler->get($shipment);
        $mainBarcode = $this->barcodeHandler->generate();
        $productCode = $this->getProductCode($shipment);

        $model->setData([
            'ship_at' => $sentDate,
            'shipment_id' => $shipment->getId(),
            'order_id' => $shipment->getOrderId(),
            'main_barcode' => $mainBarcode,
            'product_code' => $productCode,
        ]);

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
        $postNLOrder = $this->getPostNLOrder($shipment->getOrder());
        $productCode = $this->productOptions->getDefaultProductOption();

        if ($postNLOrder->getIsPakjegemak()) {
            $productCode = $this->productOptions->getDefaultPakjeGemakProductOption();
        }

        return $productCode;
    }

    /**
     * @param MagentoOrder $magentoOrder
     *
     * @return PostNLOrder
     */
    private function getPostNLOrder(MagentoOrder $magentoOrder)
    {
        /** @var PostNLOrder $postnlOrder */
        $postnlOrder = $this->orderFactory->create();

        /** @var \TIG\PostNL\Model\ResourceModel\Order\Collection $collection */
        $collection = $postnlOrder->getCollection();
        $collection->addFieldToFilter('quote_id', $magentoOrder->getQuoteId());

        // @codingStandardsIgnoreLine
        $postnlOrder = $collection->setPageSize(1)->getFirstItem();

        return $postnlOrder;
    }

    /**
     * @param $model
     */
    private function handleMultipleParcels($model)
    {
        $parcelCount = $model->getParcelCount();
        if ($parcelCount > 1) {
            $this->barcodeHandler->saveShipment($model->getEntityId(), $parcelCount);
        }
    }
}
