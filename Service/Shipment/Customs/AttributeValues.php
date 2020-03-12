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
namespace TIG\PostNL\Service\Shipment\Customs;

use Magento\Sales\Api\Data\ShipmentItemInterface;
use TIG\PostNL\Config\Provider\Globalpack;
use Magento\Catalog\Api\ProductRepositoryInterface;
use TIG\PostNL\Exception as PostNLException;
use Magento\Framework\App\Config\ScopeConfigInterface;

class AttributeValues
{
    const DEFAULT_HS_TARIFF = '000000';

    private $globalpackConfig;

    private $productRepository;

    private $scopeConfig;

    private $hasFallback = [
        'country_of_manufacture'
    ];

    /**
     * AttributeValues constructor.
     *
     * @param Globalpack                 $globalpack
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface       $scopeConfig
     */
    public function __construct(
        Globalpack $globalpack,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->globalpackConfig  = $globalpack;
        $this->productRepository = $productRepository;
        $this->scopeConfig       = $scopeConfig;
    }

    /**
     * @param string $attributeCode
     * @param ShipmentItemInterface $item
     *
     * @return mixed
     * @throws PostNLException
     */
    public function get($attributeCode, $item)
    {
        $orderItem = $item->getOrderItem();
        $discountPerItem = $orderItem->getDiscountAmount() / $orderItem->getQtyOrdered();
        $totalDiscount = $discountPerItem * $item->getQty();
        $attributeValue = $item->getPrice() - $totalDiscount;

        if (empty($attributeValue) && !in_array($attributeCode, $this->hasFallback)) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(
                __('Missing customs %1 attribute on product %2', [$attributeCode, $item->getSku()]),
                'POSTNL-0092'
            );
        }

        return $attributeValue;
    }

    /**
     * @param $item
     * @param $storeId
     *
     * @return mixed
     */
    public function getDescription($item, $storeId)
    {
        return $this->get($this->globalpackConfig->getProductDescriptionAttributeCode($storeId), $item);
    }

    /**
     * @param $item
     * @param $storeId
     *
     * @return mixed
     */
    public function getCustomsValue($item, $storeId)
    {
        return $this->get($this->globalpackConfig->getProductValueAttributeCode($storeId), $item);
    }

    /**
     * @param $item
     * @param $storeId
     *
     * @return mixed|string
     */
    public function getHsTariff($item, $storeId)
    {
        if (!$this->globalpackConfig->useHsTariff($storeId)) {
            return static::DEFAULT_HS_TARIFF;
        }

        return $this->get($this->globalpackConfig->getHsTariffAttributeCode($storeId), $item);
    }

    /**
     * @param $item
     * @param $storeId
     *
     * @return mixed
     */
    public function getCountryOfOrigin($item, $storeId)
    {
        $country = $this->get($this->globalpackConfig->getProductCountryOfOriginAttributeCode($storeId), $item);
        $country = $country ?: $this->scopeConfig->getValue('general/store_information/country_id');
        return $country ?: 'NL';
    }
}
