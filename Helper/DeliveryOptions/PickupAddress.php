<?php

namespace TIG\PostNL\Helper\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\AddressFactory;

class PickupAddress
{
    const PG_ADDRESS_TYPE = 'pakjegemak';

    private \Magento\Quote\Model\ResourceModel\Quote $quoteResource;

    private \Magento\Quote\Model\ResourceModel\Quote\Address $addressResource;

    private \Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory $addressCollectionFactory;

    /**
     * @var bool|AddressInterface
     */
    private $pickupAddress = false;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var AddressFactory
     */
    private $addressFactory;

    /**
     * @param Session $checkoutSession
     * @param AddressFactory $addressFactory
     */
    public function __construct(
        Session $checkoutSession,
        AddressFactory $addressFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource,
        \Magento\Quote\Model\ResourceModel\Quote\Address $addressResource,
        \Magento\Quote\Model\ResourceModel\Quote\Address\CollectionFactory $addressCollectionFactory
    ) {
        $this->quoteResource = $quoteResource;
        $this->addressResource = $addressResource;
        $this->addressCollectionFactory = $addressCollectionFactory;

        $this->checkoutSession = $checkoutSession;
        $this->addressFactory = $addressFactory;
    }

    /**
     * @return bool|AddressInterface
     */
    public function get()
    {
        return $this->pickupAddress;
    }

    /**
     * @param $address
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function set($address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        $this->remove();

        $this->pickupAddress = $this->create($address, $quote);
        $quote->addAddress($this->pickupAddress);
        $this->quoteResource->save($quote);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function remove()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        /** @var array|\Magento\Quote\Model\Quote\Address $foundAddress */
        $foundAddress = $this->getPakjeGemakAddressInQuote($quote->getId());
        if (!empty($foundAddress) && $foundAddress->getId()) {
            $foundAddress->isDeleted(true);
            $quote->removeAddress($foundAddress->getId());
        }

        $this->quoteResource->save($quote);
    }

    /**
     * @param int $quoteId
     *
     * @return array|\Magento\Quote\Model\Quote\Address
     */
    public function getPakjeGemakAddressInQuote($quoteId)
    {
        $collection = $this->addressCollectionFactory->create();
        $collection->addFieldToFilter('quote_id', $quoteId);
        $collection->addFieldToFilter('address_type', self::PG_ADDRESS_TYPE);
        // @codingStandardsIgnoreLine
        return $collection->setPageSize(1)->getFirstItem();
    }

    /**
     * @param                            $pgData
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote\Address
     * @throws \Exception
     */
    private function create($pgData, $quote)
    {
        $address = $this->addressFactory->create();

        $address->setQuoteId($quote->getId());
        $address->setAddressType(self::PG_ADDRESS_TYPE);
        $address->setCompany($pgData['Name']);
        $address->setCity($pgData['City']);
        $address->setCountryId($pgData['Countrycode']);
        $address->setStreet($this->getStreet($pgData));
        $address->setPostcode($pgData['Zipcode']);
        $address->setFirstname($pgData['customer']['firstname']);
        $address->setLastname($pgData['customer']['lastname']);
        $telephone = $pgData['customer']['telephone'] ?? '';
        $address->setTelephone($telephone);
        $this->addressResource->save($address);

        return $address;
    }

    /**
     * @param $address
     *
     * @return array
     */
    private function getStreet($address)
    {
        $houseNr = $address['HouseNr'];
        $houseNrExt = isset($address['HouseNrExt']) ? $address['HouseNrExt'] : null;

        $street = [$address['Street'], $houseNr, $houseNrExt];
        $street = (implode("\n", $street));

        return $street;
    }
}
