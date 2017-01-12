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
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;
use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Model\ShipmentBarcodeFactory;
use TIG\PostNL\Webservices\Endpoints\Barcode;

class SalesOrderShipmentSaveAfterEvent implements ObserverInterface
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

    /**
     * @var Barcode
     */
    private $barcode;

    /**
     * @var CollectionFactory
     */
    private $shipmentBarcodeCollectionFactory;

    /**
     * @param ShipmentFactory        $shipmentFactory
     * @param ShipmentBarcodeFactory $shipmentBarcodeFactory
     * @param CollectionFactory      $shipmentBarcodeCollectionFactory
     * @param Barcode                $barcode
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        CollectionFactory $shipmentBarcodeCollectionFactory,
        Barcode $barcode
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
        $this->barcode = $barcode;
        $this->shipmentBarcodeCollectionFactory = $shipmentBarcodeCollectionFactory;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $observer->getData('data_object');
        $mainBarcode = $this->generateBarcode();

        // @codingStandardsIgnoreLine
        //TODO: actually get & save the parcel count

        /** @var \TIG\PostNL\Model\Shipment $model */
        $model = $this->shipmentFactory->create();
        $model->setShipmentId($shipment->getId());
        $model->setMainBarcode($mainBarcode);
        $model->save();

        $parcelCount = $model->getParcelCount();
        if ($parcelCount > 1) {
            $this->saveShipmentBarcode($model->getEntityId(), $parcelCount);
        }
    }

    /**
     * Generate and save a new barcode for the just saved shipment
     *
     * @param $shipmentId
     * @param $parcelCount
     */
    private function saveShipmentBarcode($shipmentId, $parcelCount)
    {
        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentBarcode\Collection $barcodeModelCollection */
        $barcodeModelCollection = $this->shipmentBarcodeCollectionFactory->create();
        $barcodeModelCollection->load();

        for ($count = 1; $count <= $parcelCount; $count++) {
            $barcode = $this->generateBarcode();

            /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
            $barcodeModel = $this->shipmentBarcodeFactory->create();
            $barcodeModel->setShipmentId($shipmentId);
            $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);
            $barcodeModel->setNumber($count);
            $barcodeModel->setValue($barcode);

            $barcodeModelCollection->addItem($barcodeModel);
        }

        $barcodeModelCollection->save();
    }

    /**
     * CIF call to generate a new barcode
     *
     * @return \Magento\Framework\Phrase
     */
    private function generateBarcode()
    {
        $response = $this->barcode->call();

        if (!is_object($response) || !isset($response->Barcode)) {
            return __('Invalid GenerateBarcode response: %1', var_export($response, true));
        }

        return $response->Barcode;
    }
}
