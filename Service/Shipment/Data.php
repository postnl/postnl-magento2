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

use TIG\PostNL\Api\Data\ShipmentInterface;

class Data
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @param ProductOptions $productOptions
     */
    public function __construct(
        ProductOptions $productOptions
    ) {
        $this->productOptions = $productOptions;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $address
     * @param                   $contact
     * @param int               $currentShipmentNumber
     *
     * @return array
     */
    public function get(ShipmentInterface $shipment, $address, $contact, $currentShipmentNumber = 0)
    {
        $shipmentData = $this->getDefaultShipmentData($shipment, $address, $contact, $currentShipmentNumber);

        $productOptions = $this->productOptions->get($shipment);
        if ($productOptions) {
            $shipmentData['ProductOptions'] = $productOptions;
        }

        if ($shipment->isExtraCover()) {
            $shipmentData['Amounts'] = $this->getAmount($shipment);
        }

        if ($shipment->getParcelCount() > 1) {
            $shipmentData['Group'] = $this->getGroupData($shipment, $currentShipmentNumber);
        }

        return $shipmentData;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $address
     * @param                   $contact
     * @param                   $currentShipmentNumber
     *
     * @return array
     */
    private function getDefaultShipmentData(ShipmentInterface $shipment, $address, $contact, $currentShipmentNumber)
    {
        return [
            'Addresses'                => ['Address' => $address],
            'Barcode'                  => $shipment->getBarcode($currentShipmentNumber),
            'CollectionTimeStampEnd'   => '',
            'CollectionTimeStampStart' => '',
            'Contacts'                 => ['Contact' => $contact],
            'Dimension'                => ['Weight' => round($shipment->getTotalWeight())],
            'DeliveryDate'             => $shipment->getDeliveryDateFormatted(),
            'DownPartnerID'            => $shipment->getPgRetailNetworkId(),
            'DownPartnerLocation'      => $shipment->getPgLocationCode(),
            'ProductCodeDelivery'      => $shipment->getProductCode(),
        ];
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return array
     */
    private function getAmount(ShipmentInterface $shipment)
    {
        $amounts = [];
        $extraCoverAmount = $shipment->getExtraCoverAmount();

        $amounts[] = [
            'AccountName'       => '',
            'BIC'               => '',
            'IBAN'              => '',
            'AmountType'        => '02', // 01 = COD, 02 = Insured
            'Currency'          => 'EUR',
            'Reference'         => '',
            'TransactionNumber' => '',
            'Value'             => number_format($extraCoverAmount, 2, '.', ''),
        ];

        return $amounts;
    }

    private function getGroupData(ShipmentInterface $shipment, $currentShipmentNumber)
    {
        return [
            'GroupCount'    => $shipment->getParcelCount(),
            'GroupSequence' => $currentShipmentNumber,
            'GroupType'     => '03',
            'MainBarcode'   => $shipment->getMainBarcode(),
        ];
    }
}
