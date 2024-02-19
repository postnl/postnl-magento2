<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\Globalpack;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\Customs\SortItems;
use TIG\PostNL\Service\Shipment\Customs\AttributeValues;

class Customs
{
    /**
     * @var Globalpack
     */
    private $globalpackConfig;

    /**
     * @var SortItems
     */
    private $sortItems;

    private $attributeValues;

    private $customs = [
        'ShipmentType'           => 'Commercial Goods',
        'HandleAsNonDeliverable' => 'false',
        'Invoice'                => 'false',
        'Certificate'            => 'false',
        'License'                => 'false',
        'Currency'               => 'EUR'
    ];

    private $transactionData = [
        'Commercial Goods' =>  ['code' => '11', 'description' => 'Sale of goods'],
        'Returned Goods' =>    ['code' => '21', 'description' => 'Returned goods'],
        'Gift' =>              ['code' => '31', 'description' => 'Gift'],
        'Commercial Sample' => ['code' => '32', 'description' => 'Commercial sample'],
        'Documents' =>         ['code' => '91', 'description' => 'Documents'],
    ];

    /**
     * @var \Magento\Sales\Api\Data\ShipmentInterface
     */
    private $shipment;

    private $requiredInvoiceTypes = [
        'Commercial Goods',
        'Commercial Sample',
        'Returned Goods',
    ];

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Customs constructor.
     *
     * @param Globalpack           $globalpack
     * @param SortItems            $sortItems
     * @param AttributeValues      $attributeValues
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Globalpack $globalpack,
        SortItems $sortItems,
        AttributeValues $attributeValues,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->globalpackConfig = $globalpack;
        $this->sortItems = $sortItems;
        $this->attributeValues = $attributeValues;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ShipmentInterface $postnlShipment
     *
     * @return array
     */
    public function get(ShipmentInterface $postnlShipment)
    {
        $this->shipment = $postnlShipment->getShipment();

        $this->insertCustomsLicense();
        $this->insertCustomsCertificate();
        $this->insertShipmentType();
        $this->insertContentInformation();

        return $this->customs;
    }

    private function insertCustomsCertificate()
    {
        $certificate = $this->globalpackConfig->getCertificateNumber($this->shipment->getStoreId());
        if ($certificate) {
            $this->customs['Certificate']   = 'true';
            $this->customs['CertificateNr'] = $certificate;
        }
    }

    private function insertCustomsLicense()
    {
        $license = $this->globalpackConfig->getLicenseNumber($this->shipment->getStoreId());
        if ($license) {
            $this->customs['License']   = 'true';
            $this->customs['LicenseNr'] = $license;
        }
    }

    private function insertShipmentType()
    {
        $type = $this->globalpackConfig->getDefaultShipmentType($this->shipment->getStoreId());
        if ($type) {
            $this->customs['ShipmentType'] = $type;
        }

        $this->applyTransactionData($type);

        if (in_array($this->customs['ShipmentType'], $this->requiredInvoiceTypes)
            || $this->customs['License'] == 'false'
            || $this->customs['Certificate'] == 'false'
        ) {
            $this->customs['Invoice']   = 'true';
            $this->customs['InvoiceNr'] = $this->shipment->getIncrementId();
        }
    }

    /**
     * @param string $type
     */
    private function applyTransactionData($type)
    {
        if (!array_key_exists($type, $this->transactionData)) {
            return;
        }

        $transactionData = $this->transactionData[$type];

        $this->customs['TransactionCode'] = $transactionData['code'];
        $this->customs['TransactionDescription'] = $transactionData['description'];
    }

    private function insertContentInformation()
    {
        $content = [];
        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($this->sortItems->get($this->shipment) as $item) {
            $content[] = [
                'Description'     => $this->attributeValues->getDescription($item, $this->shipment->getStoreId()),
                'Quantity'        => $this->getQty($item),
                'Weight'          => $this->getWeight($item),
                'Value'           => $this->getValue($item),
                'HSTariffNr'      => $this->attributeValues->getHsTariff($item, $this->shipment->getStoreId()),
                'CountryOfOrigin' => $this->attributeValues->getCountryOfOrigin($item, $this->shipment->getStoreId())
            ];
        }

        $this->customs['Content'] = $content;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item $item
     *
     * @return float|int
     */
    private function getValue($item)
    {
        $value           = $this->attributeValues->getCustomsValue($item, $this->shipment->getStoreId());
        if (!$value) {
            $this->attributeValues->get('price', $item);
        }

        $orderItem = $item->getOrderItem();
        if ($orderItem === null) {
            $orderItem = $item;
        }

        $discountPerItem = $orderItem->getDiscountAmount() / $orderItem->getQtyOrdered();
        $totalDiscount   = $discountPerItem * $item->getQty();
        $value           = $value - $totalDiscount;

        return round($value * $this->getQty($item), 2);
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item $item  $item
     *
     * @return float|int
     */
    private function getWeight($item)
    {
        // Divide by zero not allowed.
        $weight = ($item->getWeight() ?: 1) * $this->getQty($item);

        // If weight unit is set to lbs convert it to kgs
        if ($this->getWeightUnit() == 'lbs') {
            $weight = $weight / 2.2046226218;
        }

        // convert kgs to grams because PostNL only accepts grams
        $weight = $weight * 1000;
        $weight = (int)$weight;

        return $weight;
    }

    public function getWeightUnit()
    {
        return $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment\Item|\Magento\Sales\Model\Order\Item $item  $item
     *
     * @return int
     */
    private function getQty($item)
    {
        if (!$item->getQty()) {
            // It means this is an order Item of bundle products.
            $item->setQty($item->getQtyOrdered() - ($item->getQtyShipped() - $item->getQtyCanceled()));
        }

        return $item->getQty() ?: 1;
    }
}
