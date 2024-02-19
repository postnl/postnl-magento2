<?php

use \Magento\TestFramework\Helper\Bootstrap;
use \Magento\Catalog\Model\Product\Type;
use \Magento\Customer\Model\Group;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Catalog\Model\Product;

Bootstrap::getInstance()->reinitialize();
/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Extra@Home Product')
    ->setSku('simple_eah')
    ->setPrice(10)
    ->setWeight(60000)
    ->setShortDescription("Product for Extra@Home")
    ->setTaxClassId(0)
    ->setTierPrice(
        [
            [
                'website_id' => 0,
                'cust_group' => Group::CUST_GROUP_ALL,
                'price_qty'  => 2,
                'price'      => 8,
            ],
            [
                'website_id' => 0,
                'cust_group' => Group::CUST_GROUP_ALL,
                'price_qty'  => 5,
                'price'      => 5,
            ],
            [
                'website_id' => 0,
                'cust_group' => Group::NOT_LOGGED_IN_ID,
                'price_qty'  => 3,
                'price'      => 5,
            ],
        ]
    )
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData([
        'use_config_manage_stock'   => 1,
        'qty'                       => 100,
        'is_qty_decimal'            => 0,
        'is_in_stock'               => 1,
    ])
    ->setCanSaveCustomOptions(true)
    ->setHasOptions(true)
    ->setCustomAttribute('postnl_product_type', 'extra_at_home')
    ->setCustomAttribute('postnl_parcel_count', 2)
    ->setCustomAttribute('postnl_parcel_volume', 50000)
    ->setPostnlProductType('extra_at_home')
    ->setPostnlParcelCount(2)
    ->setPostnlParcelVolume(50000);

/** @var ProductRepositoryInterface $productRepositoryFactory */
$productRepositoryFactory = $objectManager->create(ProductRepositoryInterface::class);
$productRepositoryFactory->save($product);
