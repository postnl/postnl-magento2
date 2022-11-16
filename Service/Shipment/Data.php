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
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Volume\Items\Calculate;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;

// @codingStandardsIgnoreFile
class Data
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var ContentDescription
     */
    private $contentDescription;

    /**
     * @var Calculate
     */
    private $shipmentVolume;

    /**
     * @var Customs
     */
    private $customsInfo;

    /**
     * @var LabelAndPackingslipOptions
     */
    private $labelAndPackingslipOptions;

    /**
     * @var DeliveryDateFallback
     */
    private $deliveryDateFallback;

    /**
     * @var ReturnOptions
     */
    private $returnOptions;

    /**
     * @param ProductOptions             $productOptions
     * @param ContentDescription         $contentDescription
     * @param Calculate                  $calculate
     * @param LabelAndPackingslipOptions $labelAndPackingslipOptions
     * @param Customs                    $customs
     * @param DeliveryDateFallback       $deliveryDateFallback
     * @param ReturnOptions              $returnOptions
     */
    public function __construct(
        ProductOptions             $productOptions,
        ContentDescription         $contentDescription,
        Calculate                  $calculate,
        LabelAndPackingslipOptions $labelAndPackingslipOptions,
        Customs                    $customs,
        DeliveryDateFallback       $deliveryDateFallback,
        ReturnOptions              $returnOptions
    ) {
        $this->productOptions             = $productOptions;
        $this->contentDescription         = $contentDescription;
        $this->shipmentVolume             = $calculate;
        $this->labelAndPackingslipOptions = $labelAndPackingslipOptions;
        $this->customsInfo                = $customs;
        $this->deliveryDateFallback       = $deliveryDateFallback;
        $this->returnOptions              = $returnOptions;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $address
     * @param                   $contact
     * @param int               $currentShipmentNumber
     *
     * @return array
     */
    public function get(ShipmentInterface $shipment, $address, $contact, $currentShipmentNumber = 1)
    {
        $shipmentData = $this->getDefaultShipmentData($shipment, $address, $contact, $currentShipmentNumber);
        $shipmentData = $this->setMandatoryShipmentData($shipment, $currentShipmentNumber, $shipmentData);

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
        $deliveryDate = $shipment->getDeliveryDate();
        $postnlOrder  = $shipment->getPostNLOrder();
        $shipmentType = $postnlOrder ? $postnlOrder->getType() : '';

        if (!$deliveryDate) {
            $deliveryDate = $this->getDeliveryDateFromPostNLOrder($shipment);
        }

        if (!$deliveryDate) {
            $deliveryDate = $this->deliveryDateFallback->get();
        }

        if ($shipmentType === ProductInfo::SHIPMENT_TYPE_GP || $shipmentType === ProductInfo::SHIPMENT_TYPE_EPS) {
            $deliveryDate = '';
        }

        return [
            'Addresses'                => ['Address' => $address],
            'Barcode'                  => $shipment->getBarcode($currentShipmentNumber),
            'CollectionTimeStampEnd'   => '',
            'CollectionTimeStampStart' => '',
            'Contacts'                 => ['Contact' => $contact],
            'Dimension'                => [
                    'Weight' => $this->getWeightByParcelCount(
                        $shipment->getTotalWeight(),
                        $shipment->getParcelCount()
                    )
                ],
            'DeliveryDate'             => $deliveryDate,
            'DownPartnerID'            => $shipment->getDownpartnerId(),
            'DownPartnerLocation'      => $shipment->getDownpartnerLocation(),
            'DownPartnerBarcode'       => $shipment->getDownpartnerBarcode(),
            'ProductCodeDelivery'      => $shipment->getProductCode(),
            'ReturnBarcode'            => $shipment->getReturnBarcodes($currentShipmentNumber),
            'Reference'                => $this->labelAndPackingslipOptions->getReference($shipment->getShipment())
        ];
    }

    /**
     * @param $shipment
     *
     * @return bool|false|string
     */
    private function getDeliveryDateFromPostNLOrder($shipment)
    {
        $deliveryDate = $shipment->getPostNLOrder()->getDeliveryDate();
        if ($deliveryDate) {
            return date('d-m-Y H:i:s', strtotime($deliveryDate));
        }

        return false;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param int               $currentShipmentNumber
     * @param array             $shipmentData
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    private function setMandatoryShipmentData(ShipmentInterface $shipment, $currentShipmentNumber, array $shipmentData)
    {
        $magentoShipment = $shipment->getShipment();
        if ($shipment->isExtraAtHome()) {
            $shipmentData['Content'] = $this->contentDescription->get($shipment);
            $shipmentData['Dimension']['Volume'] = $this->getVolumeByParcelCount(
                $magentoShipment->getItems(), $shipment->getParcelCount()
            );
            $shipmentData['Reference'] = $this->labelAndPackingslipOptions->getReference($magentoShipment);
        }

        if ($shipment->isGlobalPack()) {
            $shipmentData['Customs'] = $this->customsInfo->get($shipment);
        }

        if ($shipment->isExtraCover() && $currentShipmentNumber <= 1) {
            $shipmentData['Amounts'] = $this->getAmount($shipment);
        }

        if ($shipment->getParcelCount() > 1) {
            $shipmentData['Groups'] = $this->getGroupData($shipment, $currentShipmentNumber);
        }

        $productOptions = $this->productOptions->get($shipment);
        if ($productOptions) {
            $shipmentData['ProductOptions'] = $productOptions;
        }

        $smartReturnActive = $this->returnOptions->isSmartReturnActive();
        if ($smartReturnActive && $shipment->getIsSmartReturn()) {
            $shipmentData['ProductOptions']      = $this->getSmartReturnOptions();
            $shipmentData['ProductCodeDelivery'] = '2285';
        }

        return $shipmentData;
    }
    // @codingStandardsIgnoreEnd

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

    /**
     * @param $items
     * @param $count
     *
     * @return float|int
     */
    private function getVolumeByParcelCount($items, $count)
    {
        $volume = $this->shipmentVolume->get($items);
        // Devision by zero not allowed.
        return round(($volume ?: 1) / ($count ?: 1));
    }

    /**
     * @param $weight
     * @param $count
     *
     * @return float
     */
    private function getWeightByParcelCount($weight, $count)
    {
        // Devision by zero not allowed.
        $weight = round(($weight ?: 1) / ($count ?: 1), 3);
        // convert kgs to grams because PostNL only accepts grams
        $weight = $weight * 1000;

        return $weight <= 1000 ? 1000 : $weight;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentShipmentNumber
     *
     * @return array
     */
    private function getGroupData(ShipmentInterface $shipment, $currentShipmentNumber)
    {
        return [
            'Group' => [
                'GroupCount'    => $shipment->getParcelCount(),
                'GroupSequence' => $currentShipmentNumber,
                'GroupType'     => '03',
                'MainBarcode'   => $shipment->getMainBarcode(),
            ]
        ];
    }

    /**
     * @return array
     */
    private function getSmartReturnOptions()
    {
        return [
            'ProductOption' => [
                'Characteristic' => '152',
                'Option'         => '025'
            ]
        ];
    }
}
