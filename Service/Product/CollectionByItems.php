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
namespace TIG\PostNL\Service\Product;

use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Api\Data\ProductInterface;

class CollectionByItems
{
    const ATTRIBUTE_CODE = 'postnl_shipping_duration';

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroup
     */
    private $filterGroup;

    /**
     * @param ProductRepository        $productRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param FilterBuilder            $filterBuilder
     * @param FilterGroup              $filterGroup
     */
    public function __construct(
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        FilterGroup $filterGroup
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroup = $filterGroup;
    }

    /**
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * @return ProductInterface[]
     */
    public function get($items)
    {
        $this->setFilterGroups($this->mapSkus($items));
        $this->searchCriteriaBuilder->setFilterGroups([$this->filterGroup]);
        /** @var \Magento\Catalog\Api\Data\ProductSearchResultsInterface $products */
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());

        return $products->getItems();
    }

    /**
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * There is currently a known issue in the searchCriteriaBuilder which causes some data to get lost:
     * https://github.com/magento/magento2/issues/13751
     *
     * @return ProductInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByIds($items)
    {
        $products = [];
        foreach ($items as $item) {
            $products[] = $this->productRepository->getById($item->getProductId());
        }

        return $products;
    }

    /**
     * @param $items
     *
     * @return array
     */
    private function mapSkus($items)
    {
        $skus = [];
        /** @var ShipmentItemInterface|OrderItemInterface|QuoteItem $item */
        foreach ($items as $item) {
            $skus[] = $item->getSku();
        }

        return $skus;
    }

    /**
     * @param array $skus
     */
    private function setFilterGroups($skus)
    {
        $this->filterGroup->setFilters([
            // @codingStandardsIgnoreLine
            $this->filterBuilder
                ->setField(ProductInterface::SKU)
                ->setConditionType('in')
                ->setValue($skus)
                ->create()
        ]);
    }
}
