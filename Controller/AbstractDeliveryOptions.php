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

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Json\Helper\Data;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use \Magento\Checkout\Model\Session;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

abstract class AbstractDeliveryOptions extends Action
{
    /**
     * @var Data
     */
    //@codingStandardsIgnoreLine
    protected $jsonHelper;

    /**
     * @var OrderFactory
     */
    //@codingStandardsIgnoreLine
    protected $orderFactory;

    /**
     * @var OrderRepository
     */
    //@codingStandardsIgnoreLine
    protected $orderRepository;

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
     * @param Context         $context
     * @param Data            $jsonHelper
     * @param OrderFactory    $orderFactory
     * @param OrderRepository $orderRepository
     * @param Session         $checkoutSession
     * @param DeliveryDate    $deliveryDate
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        DeliveryDate $deliveryDate = null
    ) {
        $this->jsonHelper       = $jsonHelper;
        $this->orderFactory     = $orderFactory;
        $this->orderRepository  = $orderRepository;
        $this->checkoutSession  = $checkoutSession;
        $this->deliveryEndpoint = $deliveryDate;

        parent::__construct($context);
    }

    /**
     * Create json response
     *
     * @param string $data
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
            $this->jsonHelper->jsonEncode($data)
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

        $this->deliveryEndpoint->setParameters($address);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }
}
