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
    ->setName('Regular Product')
    ->setSku('simple_reg')
    ->setPrice(10)
    ->setWeight(1)
    ->setShortDescription("Product for Regular")
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
    ->setCustomAttribute('postnl_product_type', 'regular');

/** @var ProductRepositoryInterface $productRepositoryFactory */
$productRepositoryFactory = $objectManager->create(ProductRepositoryInterface::class);
$productRepositoryFactory->save($product);