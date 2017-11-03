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
namespace TIG\PostNL\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use TIG\PostNL\Api\Data\ShipmentInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ShipmentRepository as OrderShipmentRepository;
use Magento\Sales\Model\Order\Shipment\Item;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;
use TIG\PostNL\Config\Source\Options\ProductOptions;

// @codingStandardsIgnoreFile
/**
 * Too much public methods, and too much code. We can't get this file to pass the (Object Calistenics) code inspection.
 */
class Shipment extends AbstractModel implements ShipmentInterface, IdentityInterface
{
    const CACHE_TAG = 'tig_postnl_shipment';

    const FIELD_SHIPMENT_ID = 'shipment_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_MAIN_BARCODE = 'main_barcode';
    const FIELD_PRODUCT_CODE = 'product_code';
    const FIELD_SHIPMENT_TYPE = 'shipment_type';
    const FIELD_DELIVERY_DATE = 'delivery_date';
    const FIELD_IS_PAKJEGEMAK = 'is_pakjegemak';
    const FIELD_PG_LOCATION_CODE = 'pg_location_code';
    const FIELD_PG_RETAIL_NETWORK_ID = 'pg_retail_network_id';
    const FIELD_PARCEL_COUNT = 'parcel_count';
    const FIELD_SHIP_AT = 'ship_at';
    const FIELD_CONFIRMED_AT = 'confirmed_at';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_eventPrefix = 'tig_postnl_shipment';

    /**
     * @var OrderShipmentRepository $orderShipmentRepository
     */
    private $orderShipmentRepository;

    /**
     * @var TimezoneInterface
     */
    private $timezoneInterface;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var ShipmentBarcodeRepositoryInterface
     */
    private $barcodeRepository;

    /**
     * @param Context                            $context
     * @param Registry                           $registry
     * @param OrderShipmentRepository            $orderShipmentRepository
     * @param OrderFactory                       $orderFactory
     * @param AddressFactory                     $addressFactory
     * @param TimezoneInterface                  $timezoneInterface
     * @param DateTime                           $dateTime
     * @param ProductOptions                     $productOptions
     * @param ShipmentBarcodeRepositoryInterface $barcodeRepository
     * @param AbstractResource                   $resource
     * @param AbstractDb                         $resourceCollection
     * @param array                              $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderShipmentRepository $orderShipmentRepository,
        OrderFactory $orderFactory,
        AddressFactory $addressFactory,
        TimezoneInterface $timezoneInterface,
        DateTime $dateTime,
        ProductOptions $productOptions,
        ShipmentBarcodeRepositoryInterface $barcodeRepository,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $dateTime, $resource, $resourceCollection, $data);

        $this->orderShipmentRepository = $orderShipmentRepository;
        $this->timezoneInterface = $timezoneInterface;
        $this->orderFactory = $orderFactory;
        $this->addressFactory = $addressFactory;
        $this->productOptions = $productOptions;
        $this->barcodeRepository = $barcodeRepository;
    }

    /**
     * Constructor
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ResourceModel\Shipment');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->orderShipmentRepository->get($this->getShipmentId());
    }

    /**
     * @return Address
     */
    public function getShippingAddress()
    {
        $postNLOrder = $this->getPostNLOrder();
        $shipment = $this->getShipment();
        $shippingAddress = $shipment->getShippingAddress();

        if (!$postNLOrder->getIsPakjegemak()) {
            return $shippingAddress;
        }

        $pgOrderAddressId = $postNLOrder->getPgOrderAddressId();
        $order = $shipment->getOrder();
        $orderBillingId = $order->getBillingAddressId();

        $pgAddressStreet = implode("\n", $this->getPakjegemakAddress()->getStreet());

        $shippingAddress = $this->filterShippingAddress([$pgOrderAddressId, $orderBillingId], $pgAddressStreet);

        return $shippingAddress;
    }

    /**
     * @param array $ignoreAddressIds
     * @param       $ignoreStreet
     *
     * @return \Magento\Framework\DataObject
     */
    private function filterShippingAddress($ignoreAddressIds, $ignoreStreet)
    {
        $addressModel = $this->addressFactory->create();
        /** @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $addressCollection */
        $addressCollection = $addressModel->getCollection();

        $addressCollection->addFieldToFilter('entity_id', ['nin' => $ignoreAddressIds]);
        $addressCollection->addFieldToFilter('parent_id', ['eq' => $this->getOrderId()]);
        $addressCollection->addFieldToFilter('street', ['neq' => $ignoreStreet]);

        // @codingStandardsIgnoreLine
        $shippingAddress = $addressCollection->setPageSize(1)->getFirstItem();

        return $shippingAddress;
    }

    /**
     * @return Address
     */
    public function getPakjegemakAddress()
    {
        $postNLOrder = $this->getPostNLOrder();
        $pgOrderAddressId = $postNLOrder->getPgOrderAddressId();

        $PgOrderAddress = $this->addressFactory->create();
        $PgOrderAddress->load($pgOrderAddressId);

        return $PgOrderAddress;
    }

    /**
     * @return Order
     */
    public function getPostNLOrder()
    {
        $postNLOrder = $this->orderFactory->create();
        $postNLOrder->load($this->getOrderId(), 'order_id');

        return $postNLOrder;
    }

    /**
     * @return float|int
     */
    public function getTotalWeight()
    {
        $items = $this->getShipment()->getAllItems();
        $weight = 0;

        /** @var Item $item */
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        if ($weight < 1) {
            $weight = 1;
        }

        return $weight;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function getDeliveryDateFormatted($format = 'd-m-Y')
    {
        $deliveryDate = $this->getData('delivery_date');
        if (!$deliveryDate) {
            $deliveryDate = $this->getDeliveryDateByOrder();
        }

        $deliveryDate = $this->timezoneInterface->date($deliveryDate);
        $deliveryDateFormatted = $deliveryDate->format($format);

        return $deliveryDateFormatted;
    }

    /**
     * @param int
     *
     * @return $this
     */
    public function setShipmentId($value)
    {
        return $this->setData(static::FIELD_SHIPMENT_ID, $value);
    }

    /**
     * @return null|int
     */
    public function getShipmentId()
    {
        return $this->getData(static::FIELD_SHIPMENT_ID);
    }

    /**
     * @param int
     *
     * @return $this
     */
    public function setOrderId($value)
    {
        return $this->setData(static::FIELD_ORDER_ID, $value);
    }

    /**
     * @return null|int
     */
    public function getOrderId()
    {
        return $this->getData(static::FIELD_ORDER_ID);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setMainBarcode($value)
    {
        return $this->setData(static::FIELD_MAIN_BARCODE, $value);
    }

    /**
     * @param int $currentShipmentNumber
     *
     * @return string
     */
    public function getBarcode($currentShipmentNumber = 1)
    {
        if ($currentShipmentNumber == 1) {
            return $this->getMainBarcode();
        }

        $barcode = $this->barcodeRepository->getForShipment($this, $currentShipmentNumber);

        if (!$barcode) {
            return null;
        }

        return $barcode->getValue();
    }

    /**
     * @return null|string
     */
    public function getMainBarcode()
    {
        return $this->getData(static::FIELD_MAIN_BARCODE);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setProductCode($value)
    {
        return $this->setData(static::FIELD_PRODUCT_CODE, $value);
    }

    /**
     * @return null|string
     */
    public function getProductCode()
    {
        return $this->getData(static::FIELD_PRODUCT_CODE);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setShipmentType($value)
    {
        return $this->setData(static::FIELD_SHIPMENT_TYPE, $value);
    }

    /**
     * @return null|string
     */
    public function getShipmentType()
    {
        return $this->getData(static::FIELD_SHIPMENT_TYPE);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setDeliveryDate($value)
    {
        return $this->setData(static::FIELD_DELIVERY_DATE, $value);
    }

    /**
     * @return null|string
     */
    public function getDeliveryDate()
    {
        return $this->getData(static::FIELD_DELIVERY_DATE);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setIsPakjegemak($value)
    {
        return $this->setData(static::FIELD_IS_PAKJEGEMAK, $value);
    }

    /**
     * @return null|string
     */
    public function getIsPakjegemak()
    {
        return $this->getData(static::FIELD_IS_PAKJEGEMAK);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setPgLocationCode($value)
    {
        return $this->setData(static::FIELD_PG_LOCATION_CODE, $value);
    }

    /**
     * @return null|string
     */
    public function getPgLocationCode()
    {
        return $this->getData(static::FIELD_PG_LOCATION_CODE);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setPgRetailNetworkId($value)
    {
        return $this->setData(static::FIELD_PG_RETAIL_NETWORK_ID, $value);
    }

    /**
     * @return null|string
     */
    public function getPgRetailNetworkId()
    {
        return $this->getData(static::FIELD_PG_RETAIL_NETWORK_ID);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setParcelCount($value)
    {
        return $this->setData(static::FIELD_PARCEL_COUNT, $value);
    }

    /**
     * @return null|string
     */
    public function getParcelCount()
    {
        return $this->getData(static::FIELD_PARCEL_COUNT);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setShipAt($value)
    {
        return $this->setData(static::FIELD_SHIP_AT, $value);
    }

    /**
     * @return null|string
     */
    public function getShipAt()
    {
        return $this->getData(static::FIELD_SHIP_AT);
    }

    /**
     * @param string
     *
     * @return $this
     */
    public function setConfirmedAt($value)
    {
        if ($value !== null) {
            $this->_eventManager->dispatch('tig_postnl_set_confirmed_at_before', ['shipment' => $this]);
        }

        return $this->setData(static::FIELD_CONFIRMED_AT, $value);
    }

    /**
     * @return null|string
     */
    public function getConfirmedAt()
    {
        return $this->getData(static::FIELD_CONFIRMED_AT);
    }

    /**
     * Check if this shipment must be sent using Extra Cover.
     *
     * @return bool
     */
    public function isExtraCover()
    {
        $productCodeOptions = $this->getProductCodeOptions();

        if ($productCodeOptions === null) {
            return false;
        }

        if (!array_key_exists('isExtraCover', $productCodeOptions)) {
            return false;
        }

        return $productCodeOptions['isExtraCover'];
    }

    /**
     * @return bool
     */
    public function isExtraAtHome()
    {
        $productCodeOptions = $this->getProductCodeOptions();

        if ($productCodeOptions === null) {
            return false;
        }

        return $productCodeOptions['group'] == 'extra_at_home_options';
    }

    /**
     * This is static for the time being.
     *
     * @return int
     */
    public function getExtraCoverAmount()
    {
        return 500;
    }

    /**
     * @return mixed
     */
    private function getProductCodeOptions()
    {
        $productCode = $this->getProductCode();
        return $this->productOptions->getOptionsByCode($productCode);
    }

    /**
     * @return \DateTime|null
     */
    private function getDeliveryDateByOrder()
    {
        $postNLOrder  = $this->getPostNLOrder();
        $deliveryDate = $postNLOrder->getDeliveryDate();
        if (!$deliveryDate) {
            return null;
        }

        /**
         * Delivery_date => '2017-11-09 01:00:00'
         * When not created with \DateTime the timezoneInterface will return it like '2015-01-01 01:00:00'
         * or something like that. When create the DateTime object with the interface it will use the locale
         * settings and most of the times it will be day -1 which will make the delivery_date => '08-11-2017'
         */
        // @codingStandardsIgnoreLine
        return new \DateTime($deliveryDate);
    }
}
