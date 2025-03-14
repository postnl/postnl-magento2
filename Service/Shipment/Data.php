<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Source\Settings\LabelReturnSettings;
use TIG\PostNL\Config\Source\Settings\LabelSettings;
use TIG\PostNL\Config\Source\Settings\ReturnTypes;
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

    /** @var ShippingOptions */
    private $shippingOptions;

    /**
     * @param ProductOptions             $productOptions
     * @param ContentDescription         $contentDescription
     * @param Calculate                  $calculate
     * @param LabelAndPackingslipOptions $labelAndPackingslipOptions
     * @param Customs                    $customs
     * @param DeliveryDateFallback       $deliveryDateFallback
     * @param ReturnOptions              $returnOptions
     * @param ShippingOptions            $shippingOptions
     */
    public function __construct(
        ProductOptions             $productOptions,
        ContentDescription         $contentDescription,
        Calculate                  $calculate,
        LabelAndPackingslipOptions $labelAndPackingslipOptions,
        Customs                    $customs,
        DeliveryDateFallback       $deliveryDateFallback,
        ReturnOptions              $returnOptions,
        ShippingOptions            $shippingOptions
    ) {
        $this->productOptions             = $productOptions;
        $this->contentDescription         = $contentDescription;
        $this->shipmentVolume             = $calculate;
        $this->labelAndPackingslipOptions = $labelAndPackingslipOptions;
        $this->customsInfo                = $customs;
        $this->deliveryDateFallback       = $deliveryDateFallback;
        $this->returnOptions              = $returnOptions;
        $this->shippingOptions            = $shippingOptions;
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
            'ProductCodeDelivery'      => ((int)$shipment->getProductCode()) % 10000,
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
        $countryId = $shipmentData['Addresses']['Address'][0]['Countrycode'] ?? null;
        if ($shipment->isExtraAtHome()) {
            $shipmentData['Content'] = $this->contentDescription->get($shipment);
            $shipmentData['Dimension']['Volume'] = $this->getVolumeByParcelCount(
                $magentoShipment->getItems(), $shipment->getParcelCount()
            );
            $shipmentData['Reference'] = $this->labelAndPackingslipOptions->getReference($magentoShipment);
        }

        if ($this->isCustomsAllowed($shipment, $countryId)) {
            $shipmentData['Customs'] = $this->customsInfo->get($shipment);
        }

        if ($shipment->isExtraCover() && $currentShipmentNumber <= 1) {
            $shipmentData['Amounts'] = $this->getAmount($shipment);
        }

        if ($shipment->getParcelCount() > 1) {
            $shipmentData['Groups'] = $this->getGroupData($shipment, $currentShipmentNumber);
        }

        $productOptions = $this->productOptions->get($shipment);
        if (!is_array($productOptions)) {
            $productOptions = [];
        }
        $returnActive = $this->returnOptions->isReturnActive();
        // Disable return codes for packets
        if ($shipment->isInternationalPacket() || $shipment->isBoxablePackets()) {
            $returnActive = false;
        }
        if ($returnActive && $countryId === 'NL' &&
            $this->returnOptions->getReturnLabel() === LabelSettings::LABEL_RETURN
        ) {
            $productOptions[] = [
                'Characteristic' => '152',
                'Option'         => '026'
            ];
            // Fill out ReturnBarcode with the same data as Barcode in this case
            $shipmentData['ReturnBarcode'] = $shipmentData['Barcode'];
        }
        if ($returnActive && $this->returnOptions->getReturnLabel() === LabelSettings::LABEL_BOX) {
            $productOptions[] = [
                'Characteristic' => '152',
                'Option'         => '028'
            ];
            $productOptions[] = [
                'Characteristic' => '191',
                'Option'         => '001'
            ];
            //$shipmentData['ReturnBarcode'] = $shipmentData['Barcode'];
        }
        if ($returnActive && $countryId === 'NL'
            && $this->returnOptions->getReturnLabel() !== LabelSettings::LABEL_BOX
            && $this->returnOptions->getReturnLabelType() === LabelReturnSettings::LABEL_RETURN_ORDER) {
            // Mark shipment as blocked.
            $shipment->setReturnStatus($shipment::RETURN_STATUS_BLOCKED);
            $productOptions[] = [
                'Characteristic' => '191',
                'Option'         => '004'
            ];
        }

        if ($shipment->isExtraCover() && $shipment->getProductCode() === '13085') {
            $productOptions[] = [
                'Characteristic' => '004',
                'Option'         => '020'
            ];
        }

        if (!empty($productOptions)) {
            $shipmentData['ProductOptions'] = $productOptions;
        }

        $smartReturnActive = $this->returnOptions->isSmartReturnActive();
        if ($smartReturnActive && $shipment->getIsSmartReturn()) {
            $shipmentData['ProductOptions']      = $this->getSmartReturnOptions();
            $shipmentData['ProductCodeDelivery'] =
                $this->returnOptions->getReturnTo() === ReturnTypes::TYPE_FREE_POST ? '2285' : '3285';
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

        $extraCoverAmount = $shipment->getInsuredTier();

        if (empty($extraCoverAmount)) {
            $extraCoverAmount = $this->shippingOptions->getInsuredTier();
        }

        if ($extraCoverAmount == 'default') {
            $extraCoverAmount = $shipment->getExtraCoverAmount();
        }

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
        // Division by zero not allowed.
        $weight = round(($weight ?: 1) / ($count ?: 1), 3);
        // convert kgs to grams because PostNL only accepts grams
        $weight = $weight * 1000;

        return $weight;
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

    private function isCustomsAllowed(ShipmentInterface $shipment, ?string $countryId): bool
    {
        return $shipment->isGlobalPack() || // Some legacy stuff first
            $shipment->isBoxablePackets() ||
            // And add customers on all non-NL/non-BE countries.
            ($countryId !== null && $countryId !== 'NL' && $countryId !== 'BE');
    }
}
