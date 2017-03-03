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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\Shipment as OrderShipment;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * @method $this setShipmentId(string)
 * @method null|string getShipmentId
 * @method $this setOrderId(string)
 * @method null|string getOrderId
 * @method $this setMainBarcode(string)
 * @method null|string getMainBarcode
 * @method $this setProductCode(string)
 * @method null|string getProductCode
 * @method $this setShipmentType(string)
 * @method null|string getShipmentType
 * @method $this setDeliveryDate(string)
 * @method null|string getDeliveryDate
 * @method $this setIsPakjegemak(string)
 * @method null|string getIsPakjegemak
 * @method $this setPgLocationCode(string)
 * @method null|string getPgLocationCode
 * @method $this setPgRetailNetworkId(string)
 * @method null|string getPgRetailNetworkId
 * @method $this setParcelCount(string $value)
 * @method null|string getParcelCount
 * @method $this setShipAt(string)
 * @method null|string getShipAt
 * @method $this setConfirmedAt(string)
 * @method null|string getConfirmedAt
 * @method $this setCreatedAt(string)
 * @method null|string getCreatedAt
 * @method $this setUpdatedAt(string)
 * @method null|string getUpdatedAt
 */
class Shipment extends AbstractModel implements ShipmentInterface, IdentityInterface
{
    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_eventPrefix = 'tig_postnl_shipment';

    /** @var OrderShipment $orderShipment */
    private $orderShipment;

    const CACHE_TAG = 'tig_postnl_shipment';

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
     * @param Context           $context
     * @param Registry          $registry
     * @param OrderShipment     $orderShipment
     * @param OrderFactory      $orderFactory
     * @param AddressFactory    $addressFactory
     * @param TimezoneInterface $timezoneInterface
     * @param AbstractResource  $resource
     * @param AbstractDb        $resourceCollection
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderShipment $orderShipment,
        OrderFactory $orderFactory,
        AddressFactory $addressFactory,
        TimezoneInterface $timezoneInterface,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->orderShipment = $orderShipment;
        $this->timezoneInterface = $timezoneInterface;
        $this->orderFactory = $orderFactory;
        $this->addressFactory = $addressFactory;
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
     * @return OrderShipment
     */
    public function getShipment()
    {
        return $this->orderShipment->load($this->getShipmentId());
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
        $orderbillingid = $order->getBillingAddressId();

        $pgAddressStreet = implode("\n", $this->getPakjegemakAddress()->getStreet());

        $shippingAddress = $this->filterShippingAddress([$pgOrderAddressId, $orderbillingid], $pgAddressStreet);

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
        $deliveryDate = $this->timezoneInterface->date($deliveryDate);
        $deliveryDateFormatted = $deliveryDate->format($format);

        return $deliveryDateFormatted;
    }
}
