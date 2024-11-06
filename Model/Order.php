<?php

namespace TIG\PostNL\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;
use TIG\PostNL\Api\Data\OrderInterface;

// @codingStandardsIgnoreFile
/**
 * Too much public methods, and too much code. We can't get this file to pass the (Object Calisthenics) code inspection.
 */
class Order extends AbstractModel implements OrderInterface
{
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_QUOTE_ID = 'quote_id';
    const FIELD_TYPE = 'type';
    const FIELD_AC_CHARACTERISTIC = 'ac_characteristic';
    const FIELD_AC_OPTION = 'ac_option';
    const FIELD_AC_INFORMATION = 'ac_information';
    const FIELD_DELIVERY_DATE = 'delivery_date';
    const FIELD_EXPECTED_DELIVERY_TIME_START = 'expected_delivery_time_start';
    const FIELD_EXPECTED_DELIVERY_TIME_END = 'expected_delivery_time_end';
    const FIELD_IS_PAKJEGEMAK = 'is_pakjegemak';
    const FIELD_IS_STATED_ADDRESS_ONLY = 'is_stated_address_only';
    const FIELD_PG_ORDER_ADDRESS_ID = 'pg_order_address_id';
    const FIELD_PG_LOCATION_CODE = 'pg_location_code';
    const FIELD_PG_RETAIL_NETWORK_ID = 'pg_retail_network_id';
    const FIELD_PRODUCT_CODE = 'product_code';
    const FIELD_FEE = 'fee';
    const FIELD_SHIP_AT = 'ship_at';
    const FIELD_CONFIRMED_AT = 'confirmed_at';
    const FIELD_CONFIRMED = 'confirmed';
    const FIELD_PARCEL_COUNT = 'parcel_count';
    const FIELD_SHIPPING_DURATION = 'shipping_duration';
    const FIELD_INSURED_TIER = 'insured_tier';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_eventPrefix = 'tig_postnl_order';

    /**
     * @var OrderRepository $orderRepository
     */
    protected $orderRepository;

    /**
     * @var QuoteRepository $quoteRepository
     */
    private $quoteRepository;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * Order constructor.
     *
     * @param OrderRepository       $orderRepository
     * @param QuoteRepository       $quoteRepository
     * @param SerializerInterface   $serializer
     * @param Context               $context
     * @param Registry              $registry
     * @param DateTime              $dateTime
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param array                 $data
     */
    public function __construct(
        OrderRepository $orderRepository,
        QuoteRepository $quoteRepository,
        SerializerInterface $serializer,
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->orderRepository = $orderRepository;
        $this->quoteRepository = $quoteRepository;
        $this->serializer      = $serializer;
        parent::__construct($context, $registry, $dateTime, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ResourceModel\Order');
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setOrderId($value)
    {
        return $this->setData(static::FIELD_ORDER_ID, $value);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(static::FIELD_ORDER_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setQuoteId($value)
    {
        return $this->setData(static::FIELD_QUOTE_ID, $value);
    }

    /**
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(static::FIELD_QUOTE_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setType($value)
    {
        return $this->setData(static::FIELD_TYPE, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(static::FIELD_TYPE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcCharacteristic($value)
    {
        return $this->setData(static::FIELD_AC_CHARACTERISTIC, $value);
    }

    /**
     * @return string|null
     */
    public function getAcCharacteristic()
    {
        return $this->getData(static::FIELD_AC_CHARACTERISTIC);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcOption($value)
    {
        return $this->setData(static::FIELD_AC_OPTION, $value);
    }

    /**
     * @return string|null
     */
    public function getAcOption()
    {
        return $this->getData(static::FIELD_AC_OPTION);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcInformation($value)
    {
        //empty arrays shouldn't be serialized or used, so set those to null
        if (is_array($value) && empty($value)) {
            $value = null;
        }

        if (!empty($value)) {
            $value = $this->serializer->serialize($value);
        }

        return $this->setData(static::FIELD_AC_INFORMATION, $value);
    }

    /**
     * @return string|null
     */
    public function getAcInformation()
    {
        $value = $this->getData(static::FIELD_AC_INFORMATION);

        if (!empty($value)) {
            $value = $this->serializer->unserialize($value);
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setDeliveryDate($value)
    {
        return $this->setData(static::FIELD_DELIVERY_DATE, $value);
    }

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getDeliveryDate()
    {
        return $this->getData(static::FIELD_DELIVERY_DATE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeStart($value)
    {
        return $this->setData(static::FIELD_EXPECTED_DELIVERY_TIME_START, $value);
    }

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeStart()
    {
        return $this->getData(static::FIELD_EXPECTED_DELIVERY_TIME_START);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeEnd($value)
    {
        return $this->setData(static::FIELD_EXPECTED_DELIVERY_TIME_END, $value);
    }

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeEnd()
    {
        return $this->getData(static::FIELD_EXPECTED_DELIVERY_TIME_END);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setIsPakjegemak($value)
    {
        return $this->setData(static::FIELD_IS_PAKJEGEMAK, $value);
    }

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getIsPakjegemak()
    {
        return $this->getData(static::FIELD_IS_PAKJEGEMAK);
    }

    /**
     * @param int|bool $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setIsStatedAddressOnly($value): self
    {
        return $this->setData(static::FIELD_IS_STATED_ADDRESS_ONLY, $value);
    }

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getIsStatedAddressOnly()
    {
        return $this->getData(static::FIELD_IS_STATED_ADDRESS_ONLY);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgOrderAddressId($value)
    {
        return $this->setData(static::FIELD_PG_ORDER_ADDRESS_ID, $value);
    }

    /**
     * @return string
     */
    public function getPgOrderAddressId()
    {
        return $this->getData(static::FIELD_PG_ORDER_ADDRESS_ID);
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingAddress()
    {
        $shippingAddress = null;

        if (!$this->getOrderId()) {
            $addresses = $this->getShippingAddressFromQuote();

            return reset($addresses);
        }

        try {
            $order = $this->orderRepository->get($this->getOrderId());

            $addresses = $order->getAddresses();
            unset($addresses[$order->getBillingAddressId()]);
            unset($addresses[$this->getPgOrderAddressId()]);
        } catch (\Error $exception) {
            $addresses = $this->getShippingAddressFromQuote();
        }

        return reset($addresses);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getShippingAddressFromQuote()
    {
        $quote = $this->quoteRepository->get($this->getQuoteId());

        $addresses = $quote->getAllShippingAddresses();
        array_walk($addresses, function ($address, $key) use (&$addresses) {
            if ($address->getId() == $this->getPgOrderAddressId()) {
                unset($addresses[$key]);
            }
        });

        return $addresses;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface;
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBillingAddress()
    {
        $order = $this->orderRepository->get($this->getOrderId());

        return $order->getBillingAddress();
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface|null;
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPgOrderAddress()
    {
        if ($this->getIsPakjegemak()) {
            $order = $this->orderRepository->get($this->getOrderId());
            return $order->getShippingAddress();
        }

        return null;
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgLocationCode($value)
    {
        return $this->setData(static::FIELD_PG_LOCATION_CODE, $value);
    }

    /**
     * @return string
     */
    public function getPgLocationCode()
    {
        return $this->getData(static::FIELD_PG_LOCATION_CODE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgRetailNetworkId($value)
    {
        return $this->setData(static::FIELD_PG_RETAIL_NETWORK_ID, $value);
    }

    /**
     * @return string
     */
    public function getPgRetailNetworkId()
    {
        return $this->getData(static::FIELD_PG_RETAIL_NETWORK_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setProductCode($value)
    {
        return $this->setData(static::FIELD_PRODUCT_CODE, $value);
    }

    /**
     * @return int
     */
    public function getProductCode()
    {
        return $this->getData(static::FIELD_PRODUCT_CODE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setFee($value)
    {
        return $this->setData(static::FIELD_FEE, $value);
    }

    /**
     * @return float
     */
    public function getFee()
    {
        return $this->getData(static::FIELD_FEE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setShipAt($value)
    {
        return $this->setData(static::FIELD_SHIP_AT, $value);
    }

    /**
     * @return string
     */
    public function getShipAt()
    {
        return $this->getData(static::FIELD_SHIP_AT);
    }

    /**
     * @return mixed
     */
    public function getParcelCount()
    {
        return $this->getData(static::FIELD_PARCEL_COUNT);
    }

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setParcelCount($value)
    {
        return $this->setData(static::FIELD_PARCEL_COUNT, $value);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setConfirmedAt($value)
    {
        return $this->setData(static::FIELD_CONFIRMED_AT, $value);
    }

    /**
     * @return string
     */
    public function getConfirmedAt()
    {
        return $this->getData(static::FIELD_CONFIRMED_AT);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setShippingDuration($value)
    {
        return $this->setData(static::FIELD_SHIPPING_DURATION, $value);
    }

    /**
     * @return mixed
     */
    public function getShippingDuration()
    {
        return $this->getData(static::FIELD_SHIPPING_DURATION);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setConfirmed($value)
    {
        return $this->setData(static::FIELD_CONFIRMED, $value);
    }

    /**
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->getData(static::FIELD_CONFIRMED);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setInsuredTier($value)
    {
        return $this->setData(static::FIELD_INSURED_TIER, $value);
    }

    /**
     * @return string
     */
    public function getInsuredTier()
    {
        return $this->getData(static::FIELD_INSURED_TIER);
    }
}
