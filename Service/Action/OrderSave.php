<?php

namespace TIG\PostNL\Service\Action;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Shipping\DeliveryDate;

class OrderSave
{
    private OrderParams $orderParams;
    private PickupAddress $pickupAddress;
    private ProductOptions $productOptions;
    private AddressConfiguration $addressConfiguration;
    private Session $checkoutSession;
    private OrderRepository $orderRepository;
    private DeliveryDate $deliveryDate;

    public function __construct(
        Session $checkoutSession,
        OrderRepository $orderRepository,
        OrderParams $orderParams,
        PickupAddress $pickupAddress,
        ProductOptions $productOptions,
        AddressConfiguration $addressConfiguration,
        DeliveryDate $deliveryDate
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
        $this->orderParams = $orderParams;
        $this->pickupAddress = $pickupAddress;
        $this->productOptions = $productOptions;
        $this->addressConfiguration = $addressConfiguration;
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * @param OrderInterface $postnlOrder
     * @param array $params
     * @return bool
     *
     * @throws CouldNotSaveException
     * @throws Exception
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function saveDeliveryOption(OrderInterface $postnlOrder, array $params): bool
    {
        $type = $params['type'];
        $params = $this->orderParams->get($this->addSessionDataToParams($params));

        $this->setPostnlData($params, $postnlOrder);
        $this->orderRepository->save($postnlOrder);

        if ($type !== 'pickup') {
            $this->pickupAddress->remove();
        }

        if ($type === 'pickup') {
            $this->pickupAddress->set($params['pg_address']);
        }

        return true;
    }

    /**
     * @param $params
     * @param $postnlOrder
     *
     * @throws CouldNotSaveException
     */
    private function setPostnlData($params, $postnlOrder)
    {
        foreach ($params as $key => $value) {
            $postnlOrder->setData($key, $value);
        }

        $country = $params['country'];
        $shopCountry = $this->addressConfiguration->getCountry();
        $postnlOrder->setIsStatedAddressOnly(false);
        if (isset($params['stated_address_only']) && $params['stated_address_only']) {
            $postnlOrder->setIsStatedAddressOnly(true);
            $postnlOrder->setProductCode($this->productOptions->getDefaultStatedAddressOnlyProductOption($country, $shopCountry));
        }

        $postnlOrder->setAcInformation(null);
        if (isset($params['ac_information']) && $params['ac_information']) {
            $postnlOrder->setAcInformation($params['ac_information']);
        }
    }

    /**
     * @param $params
     *
     * @return mixed
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function addSessionDataToParams($params)
    {
        //If no delivery date and the type is pickup, fallback, EPS or GP then retrieve the PostNL delivery date
        if (!isset($params['date']) &&
            ($params['type'] === 'pickup' || $params['type'] === 'fallback'
                || $params['type'] === 'EPS' || $params['type'] === 'GP'
                || $params['type'] === 'Letterbox Package' || $params['type'] === 'Boxable Packet')
        ) {
            $params['date'] = $this->checkoutSession->getPostNLDeliveryDate();
        }

        $params['quote_id'] = $this->checkoutSession->getQuoteId();

        // Recalculate the delivery date if it's unknown for pickup
        if (!isset($params['date']) && $params['type'] === 'pickup') {
            $params['address']['country'] = $params['address']['Countrycode'];
            $params['address']['postcode'] = $params['address']['Zipcode'];
            $params['date'] = $this->deliveryDate->get($params['address']);
        }

        return $params;
    }
}
