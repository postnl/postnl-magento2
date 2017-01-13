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
use TIG\PostNL\Model\ShipmentFactory;

class SalesOrderShipmentSaveAfterEvent implements ObserverInterface
{
    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * @var Handlers\BarcodeHandler
     */
    private $barcodeHandler;

    /**
     * @var Handlers\SentDateHandler
     */
    private $sentDateHandler;

    /**
     * @param ShipmentFactory          $shipmentFactory
     * @param Handlers\BarcodeHandler  $barcodeHandler
     * @param Handlers\SentDateHandler $sendDateHandler
     */
    public function __construct(
        ShipmentFactory $shipmentFactory,
        Handlers\BarcodeHandler $barcodeHandler,
        Handlers\SentDateHandler $sendDateHandler
    ) {
        $this->shipmentFactory = $shipmentFactory;
        $this->barcodeHandler = $barcodeHandler;
        $this->sentDateHandler = $sendDateHandler;
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

        $model->setData([
            'ship_at' => $sentDate,
            'shipment_id' => $shipment->getId(),
            'main_barcode' => $mainBarcode,
        ]);

        $model->save();

        $this->handleMultipleParcels($model);
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
