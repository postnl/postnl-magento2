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

class AttributeValues
{
    const DEFAULT_HS_TARIFF = '000000';

    private $globalpackConfig;

    private $productRepository;

    /**
     * AttributeValues constructor.
     *
     * @param Globalpack                 $globalpack
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Globalpack $globalpack,
        ProductRepositoryInterface $productRepository
    ) {
        $this->globalpackConfig  = $globalpack;
        $this->productRepository = $productRepository;
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
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($item->getSku());
        $attributeValue = $product->getDataUsingMethod($attributeCode);

        if (empty($attributeValue)) {
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
        return $this->get($this->globalpackConfig->getProductCountryOfOriginAttributeCode($storeId), $item);
    }
}
