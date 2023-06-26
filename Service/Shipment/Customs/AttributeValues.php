<?php

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
        'country_of_manufacture',
        'special_price'
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
     * @param string                $attributeCode
     * @param ShipmentItemInterface $item
     * @param int $storeId
     *
     * @return mixed
     * @throws PostNLException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode, $item, $storeId = null)
    {
        $product = $this->productRepository->get($item->getSku(), false, $storeId);
        $attributeValue = $product->getDataUsingMethod($attributeCode);

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
        return $this->get($this->globalpackConfig->getProductValueAttributeCode($storeId), $item, $storeId);
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
