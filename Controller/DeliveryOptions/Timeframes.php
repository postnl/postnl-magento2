<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Source\LetterboxPackage\DefaultProduct;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Timeframe\Resolver;

class Timeframes extends AbstractDeliveryOptions
{
    private Calculator $calculator;
    private Resolver $timeframeResolver;
    private ProductOptions $productOptions;

    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        ShippingDuration $shippingDuration,
        Calculator $calculator,
        Resolver $timeframeResolver,
        ProductOptions $productOptions
    ) {
        $this->calculator = $calculator;
        $this->timeframeResolver = $timeframeResolver;
        $this->productOptions = $productOptions;

        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
        );
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \TIG\PostNL\Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse($this->timeframeResolver->getFallBackResponse(1));
        }

        $rateRequest = $this->getRateRequest();
        $price = $this->calculator->getPriceWithTax($rateRequest);

        if (!isset($price['price'])) {
            return false;
        }

        $result = $this->timeframeResolver->processTimeframes($params['address']);
        if (($result['letterbox_package'] ?? false) === true) {
            $defaultProduct = $this->productOptions->getDefaultLetterboxPackageProductSetting();

            if ($defaultProduct === DefaultProduct::LETTERBOX_PRODUCT_CUSTOMER_CHOICE) {
                $result = $this->appendCustomerChoiceLetterboxPrices($result, $rateRequest, (float)$price['price']);
                $result['price'] = null;
                return $this->jsonResponse($result);
            }

            $letterboxPrice = $this->calculator->getLetterboxAlternativePriceWithTax($rateRequest, $defaultProduct);
            if ($letterboxPrice !== null) {
                $price['price'] = $letterboxPrice;
            }

            // For fixed default letterbox products (2928/2948), keep the notice text response
            // but also expose the resolved price on the same option.
            if (isset($result['timeframes'][0][0]) && is_array($result['timeframes'][0][0])) {
                $result['timeframes'][0][0]['price'] = (float)$price['price'];
            }
        }

        $result['price'] = $price['price'];
        return $this->jsonResponse($result);
    }

    private function appendCustomerChoiceLetterboxPrices(
        array $result,
        RateRequest $rateRequest,
        float $fallbackPrice
    ): array
    {
        if (!isset($result['timeframes'][0]) || !is_array($result['timeframes'][0])) {
            return $result;
        }

        foreach ($result['timeframes'][0] as $index => $option) {
            if (!is_array($option) || !isset($option['option'])) {
                continue;
            }

            $productCode = null;
            if ($option['option'] === ProductInfo::OPTION_LETTERBOX_PACKAGE_24) {
                $productCode = DefaultProduct::LETTERBOX_PRODUCT_2928;
            }

            if ($option['option'] === ProductInfo::OPTION_LETTERBOX_PACKAGE_48) {
                $productCode = DefaultProduct::LETTERBOX_PRODUCT_2948;
            }

            if ($productCode === null) {
                continue;
            }

            $calculatedPrice = $this->calculator->getLetterboxAlternativePriceWithTax($rateRequest, $productCode);
            $result['timeframes'][0][$index]['price'] = $calculatedPrice ?? $fallbackPrice;
        }

        return $result;
    }
}
