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
namespace TIG\PostNL\Controller\Pakjegemak;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;
use TIG\PostNL\Service\Order\CurrentPostNLOrder;
use TIG\PostNL\Service\Wrapper\QuoteInterface;

class Address extends Action
{
    /**
     * @var PickupAddress
     */
    private $pickupAddress;
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var QuoteInterface
     */
    private $quote;
    /**
     * @var CurrentPostNLOrder
     */
    private $currentPostNLOrder;

    /**
     * @param Context            $context
     * @param PickupAddress      $pickupAddress
     * @param JsonFactory        $resultJsonFactory
     * @param QuoteInterface     $quote
     * @param CurrentPostNLOrder $currentPostNLOrder
     */
    public function __construct(
        Context $context,
        PickupAddress $pickupAddress,
        JsonFactory $resultJsonFactory,
        QuoteInterface $quote,
        CurrentPostNLOrder $currentPostNLOrder
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
        $this->currentPostNLOrder = $currentPostNLOrder;
        $this->pickupAddress = $pickupAddress;
        $this->quote = $quote;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $isPakjegemakOrder = $this->isPakjegemakOrder();
        $response->setData([
            'isPakjegemakOrder' => $isPakjegemakOrder,
            'address' => $isPakjegemakOrder ? $this->getAddress() : null,
        ]);

        return $response;
    }

    /**
     * @return array
     */
    private function getAddress()
    {
        $quoteId = $this->quote->getQuoteId();
        $address = $this->pickupAddress->getPakjeGemakAddressInQuote($quoteId);

        if (!$address) {
            return null;
        }

        return $this->formatData($address);
    }

    /**
     * @return bool
     */
    private function isPakjegemakOrder()
    {
        $postNLOrder = $this->currentPostNLOrder->get();

        if ($postNLOrder === null) {
            return false;
        }

        return $postNLOrder->getType() == 'pickup';
    }

    /**
     * @param $address
     *
     * @return array
     */
    private function formatData($address)
    {
        return [
            'company'   => $address->getCompany(),
            'prefix'    => $address->getPrefix(),
            'firstname' => null,
            'lastname'  => null,
            'suffix'    => $address->getSuffix(),
            'street'    => $address->getStreet(),
            'city'      => $address->getCity(),
            'region'    => $address->getRegion(),
            'postcode'  => $address->getPostcode(),
            'countryId' => $address->getCountryId(),
            'telephone' => $address->getTelephone(),
        ];
    }
}
