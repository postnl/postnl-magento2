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
namespace TIG\PostNL\Service\Quote;

use TIG\PostNL\Service\Wrapper\QuoteInterface as CheckoutSession;
use Magento\Quote\Model\Quote as MagentoQuote;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Product\CollectionByItems;
use Magento\Catalog\Api\Data\ProductInterface;

class ShippingDuration
{
    const ATTRIBUTE_CODE = 'postnl_shipping_duration';

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Webshop
     */
    private $webshopConfiguration;

    /**
     * @var CollectionByItems
     */
    private $productCollection;

    /**
     * ShippingDuration constructor.
     *
     * @param CheckoutSession       $checkoutSession
     * @param Webshop               $webshopConfiguration
     * @param CollectionByItems $collectionByItems
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        Webshop $webshopConfiguration,
        CollectionByItems $collectionByItems
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->webshopConfiguration = $webshopConfiguration;
        $this->productCollection = $collectionByItems;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get()
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$quote) {
            return $this->webshopConfiguration->getShippingDuration();
        }

        return $this->getProvidedByQuote($quote);
    }

    /**
     * @param MagentoQuote $quote
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProvidedByQuote($quote)
    {
        $store    = $quote->getStoreId();
        $products = $this->productCollection->getByIds($quote->getAllItems());

        $shippingDurations = array_map(function (ProductInterface $product) {
            $attribute = $product->getCustomAttribute(static::ATTRIBUTE_CODE);
            if (!$attribute) {
                return $this->webshopConfiguration->getShippingDuration();
            }
            return $attribute->getValue();
        }, $products);

        $itemsDuration = $this->getItemsDuration($shippingDurations);
        if (false === $itemsDuration || !is_numeric($itemsDuration)) {
            return $this->webshopConfiguration->getShippingDuration($store);
        }

        return $itemsDuration < 0 ? 1 : round($itemsDuration, 0);
    }

    /**
     * @param $shippingDurations
     *
     * @return bool|mixed
     */
    private function getItemsDuration($shippingDurations)
    {
        if (empty($shippingDurations)) {
            return false;
        }

        return max($shippingDurations);
    }
}
