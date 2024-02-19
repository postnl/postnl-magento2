<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Phrase;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

class Save extends AbstractDeliveryOptions
{
    /**
     * @var OrderParams
     */
    private $orderParams;

    /**
     * @var PickupAddress
     */
    private $pickupAddress;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * @param Context              $context
     * @param OrderRepository      $orderRepository
     * @param QuoteToRateRequest   $quoteToRateRequest
     * @param OrderParams          $orderParams
     * @param Session              $checkoutSession
     * @param PickupAddress        $pickupAddress
     * @param ShippingDuration     $shippingDuration
     * @param ProductOptions       $productOptions
     * @param DeliveryDate         $deliveryEndpoint
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        QuoteToRateRequest $quoteToRateRequest,
        OrderParams $orderParams,
        Session $checkoutSession,
        PickupAddress $pickupAddress,
        ShippingDuration $shippingDuration,
        ProductOptions $productOptions,
        DeliveryDate $deliveryEndpoint,
        AddressConfiguration $addressConfiguration
    ) {
        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
            $deliveryEndpoint
        );

        $this->orderParams          = $orderParams;
        $this->pickupAddress        = $pickupAddress;
        $this->productOptions       = $productOptions;
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @return ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['type'])) {
            return $this->jsonResponse(__('No Type specified'));
        }

        $saved = $this->saveDeliveryOption($params);

        try {
            return $this->jsonResponse($saved);
        } catch (LocalizedException $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        }
    }

    /**
     * @param $params
     *
     * @return Phrase
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function saveDeliveryOption($params)
    {
        $type          = $params['type'];
        $params        = $this->orderParams->get($this->addSessionDataToParams($params));
        $postnlOrder   = $this->getPostNLOrderByQuoteId($params['quote_id']);

        $this->savePostNLOrderData($params, $postnlOrder);

        if ($type != 'pickup') {
            $this->pickupAddress->remove();
        }

        if ($type == 'pickup') {
            $this->pickupAddress->set($params['pg_address']);
        }

        return __('ok');
    }

    /**
     * @param $params
     * @param $postnlOrder
     *
     * @throws CouldNotSaveException
     */
    private function savePostNLOrderData($params, $postnlOrder)
    {
        foreach ($params as $key => $value) {
            $postnlOrder->setData($key, $value);
        }

        $country     = $params['country'];
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

        $this->orderRepository->save($postnlOrder);
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
        if (!isset($params['date']) && $params['type'] == 'pickup') {
            $params['address']['country'] = $params['address']['Countrycode'];
            $params['address']['postcode'] = $params['address']['Zipcode'];
            $params['date'] = $this->getDeliveryDay($params['address']);
        }

        return $params;
    }
}
