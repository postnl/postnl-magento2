<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__.'/../default_rollback.php';

Bootstrap::getInstance()->loadArea(Magento\Framework\App\Area::AREA_FRONTEND);

try {
    /** @var Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
    $productRepository = Bootstrap::getObjectManager()
        ->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
    $product = $productRepository->get('simple_backorderable');
} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
    $product = Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
    $product->setTypeId('simple')
        ->setId(2)
        ->setAttributeSetId(4)
        ->setName('Simple In Stock Product')
        ->setSku('simple_in_stock')
        ->setPrice(15)
        ->setTaxClassId(0)
        ->setMetaTitle('meta title')
        ->setMetaKeyword('meta keyword')
        ->setMetaDescription('meta description')
        ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
        ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
        ->setStockData(
            [
                'qty'                   => 50,
                'is_in_stock'           => 1,
                'backorders'            => 1,
                'use_config_backorders' => 0
            ]
        );

    $product->save();
}

$addressData    = [
    'region' => 'CA',
    'postcode' => '11111',
    'lastname' => 'lastname',
    'firstname' => 'firstname',
    'street' => 'street',
    'city' => 'Los Angeles',
    'email' => 'admin@example.com',
    'telephone' => '11111111',
    'country_id' => 'US'
];

$billingAddress = Bootstrap::getObjectManager()->create(
    \Magento\Quote\Model\Quote\Address::class,
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

/** @var \Magento\Store\Api\Data\StoreInterface $store */
$store = Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get(\Magento\Store\Model\StoreManagerInterface::class)
    ->getStore();


$couldCreateQuote = false;
try {
    /** @var \Magento\Quote\Model\Quote $quote */
    $quote = Bootstrap::getObjectManager()->create(\Magento\Quote\Model\Quote::class);
    $quote->setCustomerIsGuest(true)
          ->setStoreId($store->getId())
          ->setReservedOrderId('instock01')
          ->setBillingAddress($billingAddress)
          ->setShippingAddress($shippingAddress)
          ->addProduct($product);
    $quote->getPayment()->setMethod('checkmo');
    $quote->setIsMultiShipping('1');
    $quote->collectTotals();
    $quote->save();

    /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
    $quoteIdMask = Bootstrap::getObjectManager()
                            ->create(\Magento\Quote\Model\QuoteIdMaskFactory::class)
                            ->create();
    $quoteIdMask->setQuoteId($quote->getId());
    $quoteIdMask->setDataChanges(true);
    $quoteIdMask->save();
    $couldCreateQuote = true;
}catch(LocalizedException $exception) {
    $couldCreateQuote = false;
}
