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
namespace TIG\PostNL\Service\Handler;

use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Model\ShipmentBarcodeFactory;
use TIG\PostNL\Webservices\Endpoints\Barcode as BarcodeEndpoint;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;

class BarcodeHandler
{
    /**
     * @var BarcodeEndpoint
     */
    private $barcodeEndpoint;

    /**
     * @var CollectionFactory
     */
    private $shipmentBarcodeCollectionFactory;

    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

    /**
     * @param BarcodeEndpoint        $barcodeEndpoint
     * @param ShipmentBarcodeFactory $shipmentBarcodeFactory
     * @param CollectionFactory      $shipmentBarcodeCollectionFactory
     */
    public function __construct(
        BarcodeEndpoint $barcodeEndpoint,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        CollectionFactory $shipmentBarcodeCollectionFactory
    ) {
        $this->barcodeEndpoint = $barcodeEndpoint;
        $this->shipmentBarcodeCollectionFactory = $shipmentBarcodeCollectionFactory;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
    }

    /**
     * CIF call to generate a new barcode
     *
     * @return \Magento\Framework\Phrase
     */
    public function generate()
    {
        $response = $this->barcodeEndpoint->call();

        if (!is_object($response) || !isset($response->Barcode)) {
            return __('Invalid GenerateBarcode response: %1', var_export($response, true));
        }

        return $response->Barcode;
    }

    /**
     * Generate and save a new barcode for the just saved shipment
     *
     * @param $shipmentId
     * @param $parcelCount
     */
    public function saveShipment($shipmentId, $parcelCount)
    {
        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentBarcode\Collection $barcodeModelCollection */
        $barcodeModelCollection = $this->shipmentBarcodeCollectionFactory->create();
        $barcodeModelCollection->load();

        for ($count = 1; $count <= $parcelCount; $count++) {
            $barcode = $this->generate();

            /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
            $barcodeModel = $this->shipmentBarcodeFactory->create();
            $barcodeModel->setParentId($shipmentId);
            $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);
            $barcodeModel->setNumber($count);
            $barcodeModel->setValue($barcode);

            $barcodeModelCollection->addItem($barcodeModel);
        }

        $barcodeModelCollection->save();
    }
}
