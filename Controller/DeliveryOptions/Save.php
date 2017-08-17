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
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Checkout\Model\Session;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;

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
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @param Context            $context
     * @param OrderFactory       $orderFactory
     * @param OrderRepository    $orderRepository
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param OrderParams        $orderParams
     * @param Session            $checkoutSession
     * @param PickupAddress      $pickupAddress
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        QuoteToRateRequest $quoteToRateRequest,
        OrderParams $orderParams,
        Session $checkoutSession,
        PickupAddress $pickupAddress
    ) {
        parent::__construct(
            $context,
            $orderFactory,
            $checkoutSession,
            $quoteToRateRequest
        );

        $this->orderParams     = $orderParams;
        $this->pickupAddress   = $pickupAddress;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \TIG\PostNL\Exception
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
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveDeliveryOption($params)
    {
        $type          = $params['type'];
        $params        = $this->orderParams->get($this->addSessionDataToParams($params));
        $postnlOrder   = $this->getPostNLOrderByQuoteId($params['quote_id']);

        foreach ($params as $key => $value) {
            $postnlOrder->setData($key, $value);
        }

        $this->orderRepository->save($postnlOrder);

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
     *
     * @codingStandardsIgnoreLine
     * @todo : When type is pickup the delivery Date needs to be recalculated,
     *
     * @return mixed
     */
    private function addSessionDataToParams($params)
    {
        if (!isset($params['date']) && $params['type'] == 'pickup') {
            $params['date'] = $this->checkoutSession->getPostNLDeliveryDate();
        }

        $params['quote_id'] = $this->checkoutSession->getQuoteId();

        return $params;
    }
}
