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

namespace TIG\PostNL\Service\Order;

use Magento\Quote\Model\Quote\Address;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

class FirstDeliveryDate
{
    /** @var DeliveryDate */
    private $endpoint;

    /** @var QuoteInterface */
    private $quote;

    /** @var ShippingOptions $shippingConfig */
    private $shippingConfig;

    /** @var ShippingDuration */
    private  $shippingDuration;

    /**
     * FirstDeliveryDate constructor.
     *
     * @param DeliveryDate $endpoint
     * @param QuoteInterface $quote
     * @param ShippingOptions $shippingConfig
     * @param ShippingDuration $shippingDuration
     */
    public function __construct(
        DeliveryDate $endpoint,
        QuoteInterface $quote,
        ShippingOptions $shippingConfig,
        ShippingDuration $shippingDuration
    ) {
        $this->endpoint = $endpoint;
        $this->quote = $quote;
        $this->shippingConfig = $shippingConfig;
        $this->shippingDuration = $shippingDuration;
    }

    /**
     * @param $postcode
     * @param $country
     *
     * @return string|null
     */
    public function get($postcode, $country)
    {
        $address = $this->quote->getShippingAddress();

        if ($address === null) {
            return null;
        }

        $address->setPostcode($postcode);
        $address->setCountryId($country);

        if (!$this->validateCountry($address)) {
            return null;
        }

        return $this->getDeliveryDate($address);
    }

    /**
     * @param OrderInterface $order
     *
     * @return null|OrderInterface
     */
    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();
        if ($address === null) {
            return null;
        }

        if (!$this->validateCountry($address)) {
            return null;
        }

        if (!$this->shippingConfig->isDeliverydaysActive()) {
            $order->setDeliveryDate(null);

            return $order;
        }

        $order->setDeliveryDate($this->getDeliveryDate($address));
        return $order;
    }

    /**
     * @param Address $address
     *
     * @return bool
     */
    private function validateCountry(Address $address)
    {
        return in_array($address->getCountryId(), ['NL', 'BE']);
    }

    /**
     * @param $address
     * @return null|string
     */
    private function getDeliveryDate(Address $address)
    {
        try {
            $this->endpoint->updateApiKey($this->quote->getStoreId());
            $shippingDuration = $this->shippingDuration->get();
            $this->endpoint->updateParameters([
                'country'  => $address->getCountryId(),
                'postcode' => $address->getPostcode(),
            ], $shippingDuration);

            $response = $this->endpoint->call();
        } catch (\Exception $exception) {
            return null;
        }

        if (is_object($response) && $response->DeliveryDate) {
            return $response->DeliveryDate;
        }

        return null;
    }
}
