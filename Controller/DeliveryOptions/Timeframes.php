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
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Checkout\Model\Session;

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
     * @param Context            $context
     * @param OrderFactory       $orderFactory
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param AddressEnhancer    $addressEnhancer
     * @param DeliveryDate       $deliveryDate
     * @param TimeFrame          $timeFrame
     * @param Calculator         $calculator
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame,
        Calculator $calculator
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->timeFrameEndpoint = $timeFrame;
        $this->calculator        = $calculator;

        parent::__construct(
            $context,
            $orderFactory,
            $checkoutSession,
            $quoteToRateRequest,
            $deliveryDate
        );
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse(__('No Address data found.'));
        }

        $this->addressEnhancer->set($params['address']);

        try {
            $price = $this->calculator->price($this->getRateRequest());
            return $this->jsonResponse([
                'price' => $price['price'],
                'timeframes' => $this->getValidResponeType(),
            ]);
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        }
    }

    /**
     * @param $address
     *
     * @return array|\Magento\Framework\Phrase
     */
    private function getPosibleDeliveryDays($address)
    {
        $startDate  = $this->getDeliveryDay($address);

        return $this->getTimeFrames($address, $startDate);
    }

    /**
     * @return array|\Magento\Framework\Phrase
     */
    private function getValidResponeType()
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            //@codingStandardsIgnoreLine
            return ['error' => __('%1 : %2', $address['error']['code'], $address['error']['message'])];
        }

        return $this->getPosibleDeliveryDays($address);
    }

    /**
     * CIF call to get the timeframes.
     * @param $address
     * @param $startDate
     *
     * @return array|\Magento\Framework\Phrase
     */
    private function getTimeFrames($address, $startDate)
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->timeFrameEndpoint->setStoreId($storeId);
        $this->timeFrameEndpoint->setParameters($address, $startDate);

        return $this->timeFrameEndpoint->call();
    }
}
