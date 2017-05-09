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
namespace TIG\PostNL\Service\Shipment;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product\Type;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use TIG\PostNL\Model\Product\Attribute\Source\Type as PostNLType;

class ParcelCount
{
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

    const ATTRIBUTE_PARCEL_COUNT     = 'postnl_parcel_count';

    const WEIGHT_PER_PARCEL          = 20000;

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
     * @param ShipmentInterface $shipment
     *
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    public function get(ShipmentInterface $shipment)
    {
        /** @var ProductInterface[] $products */
        $products = $this->getProductDictionary($shipment->getItems());

        return $this->calculate($products, $shipment);
    }

    /**
     * @param ProductInterface[] $products
     * @param ShipmentInterface $shipment
     *
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    private function calculate($products, ShipmentInterface $shipment)
    {
        $parcelCount    = 0;
        $shipmentWeight = $shipment->getTotalWeight();

        if (empty($products)) {
            $remainingParcelCount = ceil($shipmentWeight / self::WEIGHT_PER_PARCEL);
            $remainingParcelCount = $remainingParcelCount < 1 ? 1 : $remainingParcelCount;
            return $parcelCount + $remainingParcelCount;
        }

        foreach ($shipment->getItems() as $item) {
            $product = $products[$item->getProductId()];
            $productParcelCount = $product->getCustomAttribute(self::ATTRIBUTE_PARCEL_COUNT);
            $parcelCount += ($productParcelCount->getValue() * $item->getQty());
        }

        return $parcelCount;
    }

    /**
     * @param ShipmentItemInterface[] $items
     *
     * @return ProductInterface[]
     */
    private function getProductDictionary($items)
    {
        $shipmentItems = $items->getItems();
        $skus = array_map(function (ShipmentItemInterface $item) {
            return $item->getSku();
        }, $shipmentItems);

        $this->setFilterGroups($skus);

        $this->searchCriteriaBuilder->setFilterGroups([$this->filterGroup]);
        /** @var \Magento\Catalog\Api\Data\ProductSearchResultsInterface $products */
        $products = $this->productRepository->getList($this->searchCriteriaBuilder->create());

        return array_filter($products->getItems(), function (ProductInterface $product) {
            $attribute = $product->getCustomAttribute(PostNLType::POSTNL_PRODUCT_TYPE);
            $value = $attribute !== null ? $attribute->getValue() : false;
            return $value == PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME;
        });

    }

    /**
     * @param array $skus
     */
    private function setFilterGroups($skus)
    {
        $this->filterGroup->setFilters([
            $this->filterBuilder
                ->setField(ProductInterface::SKU)
                ->setConditionType('in')
                ->setValue($skus)
                ->create()
        ]);
    }
}
