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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
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
     * @param ShipmentFactory        $shipmentFactory
     * @param ShipmentBarcodeFactory $shipmentBarcodeFactory
     * @param Barcode                $barcode
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        Barcode $barcode
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
        $this->barcode = $barcode;
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

        /** @var \TIG\PostNL\Model\Shipment $model */
        $model = $this->shipmentFactory->create();
        $model->setData('shipment_id', $shipment->getId());
        $model->save();

        $barcode = $this->generateBarcode();

        /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
        $barcodeModel = $this->shipmentBarcodeFactory->create();
        $barcodeModel->setData('shipment_id', $shipment->getId());
        $barcodeModel->setData('type', $barcodeModel::BARCODE_TYPE_SHIPMENT);
        $barcodeModel->setData('number', 0); //TODO: save the correct number
        $barcodeModel->setData('value', $barcode);
        $barcodeModel->save();
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
