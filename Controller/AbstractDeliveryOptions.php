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
namespace TIG\PostNL\Controller;

use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;

abstract class AbstractDeliveryOptions extends Action
{
    /**
     * @var OrderFactory
     */
    //@codingStandardsIgnoreLine
    protected $orderFactory;

    /**
     * @var Session
     */
    //@codingStandardsIgnoreLine
    protected $checkoutSession;

    /**
     * @var DeliveryDate
     */
    //@codingStandardsIgnoreLine
    protected $deliveryEndpoint;

    /**
     * @var QuoteToRateRequest
     */
    private $quoteToRateRequest;

    /**
     * @param Context            $context
     * @param OrderFactory       $orderFactory
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param DeliveryDate       $deliveryDate
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        DeliveryDate $deliveryDate = null
    ) {
        $this->orderFactory       = $orderFactory;
        $this->checkoutSession    = $checkoutSession;
        $this->deliveryEndpoint   = $deliveryDate;
        $this->quoteToRateRequest = $quoteToRateRequest;

        parent::__construct($context);
    }

    /**
     * Create json response
     *
     * @param string $data
     * @param int    $code
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    //@codingStandardsIgnoreLine
    protected function jsonResponse($data = '', $code = null)
    {
        $response = $this->getResponse();

        if ($code !== null) {
            $response->setStatusCode($code);
        }

        return $response->representJson(
            \Zend_Json::encode($data)
        );
    }

    /**
     * @param $quoteId
     *
     * @return \TIG\PostNL\Model\Order
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLOrderByQuoteId($quoteId)
    {
        /** @var \TIG\PostNL\Model\Order $postnlOrder */
        $postnlOrder = $this->orderFactory->create();

        /** @var \TIG\PostNL\Model\ResourceModel\Order\Collection $collection */
        $collection = $postnlOrder->getCollection();
        $collection->addFieldToFilter('quote_id', $quoteId);

        // @codingStandardsIgnoreLine
        $postnlOrder = $collection->setPageSize(1)->getFirstItem();

        return $postnlOrder;
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @param array $address
     *
     * @return array
     */
    //@codingStandardsIgnoreLine
    protected function getDeliveryDay($address)
    {
        if ($this->checkoutSession->getPostNLDeliveryDate()) {
            return $this->checkoutSession->getPostNLDeliveryDate();
        }

        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->deliveryEndpoint->setStoreId($storeId);
        $this->deliveryEndpoint->setParameters($address);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }

    /**
     * @return RateRequest
     */
    // @codingStandardsIgnoreLine
    protected function getRateRequest()
    {
        $request = $this->getRequest();
        $address = $request->getParam('address');

        /** @var RateRequest $request */
        $request = $this->quoteToRateRequest->get();
        $request->setDestCountryId($address['country']);
        $request->setDestPostcode($address['postcode']);

        return $request;
    }
}
