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
namespace TIG\PostNL\Controller\DeliveryOptions;

use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;
use TIG\PostNL\Config\CheckoutConfiguration\IsDeliverDaysActive;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use TIG\PostNL\Service\Quote\ShippingDuration;

// @codingStandardsIgnoreFile
class Timeframes extends AbstractDeliveryOptions
{
    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var  TimeFrame
     */
    private $timeFrameEndpoint;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var IsDeliverDaysActive
     */
    private $isDeliveryDaysActive;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @param Context                      $context
     * @param OrderRepository              $orderRepository
     * @param Session                      $checkoutSession
     * @param QuoteToRateRequest           $quoteToRateRequest
     * @param AddressEnhancer              $addressEnhancer
     * @param DeliveryDate                 $deliveryDate
     * @param TimeFrame                    $timeFrame
     * @param Calculator                   $calculator
     * @param IsDeliverDaysActive          $isDeliverDaysActive
     * @param ShippingDuration             $shippingDuration
     * @param LetterboxPackage             $letterboxPackage
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame,
        Calculator $calculator,
        IsDeliverDaysActive $isDeliverDaysActive,
        ShippingDuration $shippingDuration,
        LetterboxPackage $letterboxPackage
    ) {
        $this->addressEnhancer              = $addressEnhancer;
        $this->timeFrameEndpoint            = $timeFrame;
        $this->calculator                   = $calculator;
        $this->isDeliveryDaysActive         = $isDeliverDaysActive;
        $this->letterboxPackage             = $letterboxPackage;

        parent::__construct(
            $context,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
            $deliveryDate
        );
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \TIG\PostNL\Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse($this->getFallBackResponse(1));
        }

        $price = $this->calculator->price($this->getRateRequest(), null, null, true);

        $quote = $this->checkoutSession->getQuote();
        $cartItems = $quote->getAllItems();
        if ($this->letterboxPackage->isLetterboxPackage($cartItems, false) && $params['address']['country'] == 'NL') {
            return $this->jsonResponse($this->getLetterboxPackageResponse($price));
        }

        if (!$this->isDeliveryDaysActive->getValue()) {
            return $this->jsonResponse($this->getFallBackResponse(2, $price['price']));
        }

        $this->addressEnhancer->set($params['address']);

        try {
            return $this->jsonResponse($this->getValidResponeType($price['price']));
        } catch (\Exception $exception) {
            return $this->jsonResponse($this->getFallBackResponse(3, $price['price']));
        }
    }

    /**
     * @param $address
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getPossibleDeliveryDays($address)
    {
        $startDate  = $this->getDeliveryDay($address);

        return $this->getTimeFrames($address, $startDate);
    }

    /**
     * @param $price
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getValidResponeType($price)
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            return [
                'error'      => __('%1 : %2', $address['error']['code'], $address['error']['message']),
                'price'      => $price,
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }

        return [
            'price'      => $price,
            'timeframes' => $this->getPossibleDeliveryDays($address)
        ];
    }

    /**
     * CIF call to get the timeframes.
     *
     * @param $address
     * @param $startDate
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getTimeFrames($address, $startDate)
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->timeFrameEndpoint->updateApiKey($storeId);
        $this->timeFrameEndpoint->fillParameters($address, $startDate);

        return $this->timeFrameEndpoint->call();
    }

    /**
     * @param $error
     * @param $price
     * @return array
     */
    private function getFallBackResponse($error = 0, $price = null)
    {
        $errorMessage = isset($this->returnErrors[$error]) ? $this->returnErrors[$error] : '';
        return [
            'error'      => __($errorMessage),
            'price'      => $price,
            'timeframes' => [[['fallback' => __('At the first opportunity')]]]
        ];
    }

    private function getLetterboxPackageResponse($price)
    {
        return [
            'price'             => $price,
            'letterbox_package' => true,
            'timeframes'        => [[['letterbox_package' => __('Your order will fit through the letterbox and will be '
            . 'delivered from Tuesday to Saturday.')]]]
        ];
    }
}
