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
namespace TIG\PostNL\Observer\SalesOrderSaveAfter;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Order\ProductCodeAndType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use TIG\PostNL\Service\Parcel\Order\Count as ParcelCount;
use \TIG\PostNL\Service\Options\ItemsToOption;

class CreatePostNLOrder implements ObserverInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var ParcelCount
     */
    private $parcelCount;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var ProductCodeAndType
     */
    private $productCode;

    /**
     * @var ItemsToOption
     */
    private $itemsToOption;

    /**
     * @param OrderRepository    $orderRepository
     * @param ParcelCount        $count
     * @param ItemsToOption      $itemsToOption
     * @param ProductCodeAndType $productCode
     * @param Data               $helper
     */
    public function __construct(
        OrderRepository $orderRepository,
        ParcelCount $count,
        ItemsToOption $itemsToOption,
        ProductCodeAndType $productCode,
        Data $helper
    ) {
        $this->orderRepository = $orderRepository;
        $this->parcelCount = $count;
        $this->itemsToOption = $itemsToOption;
        $this->helper = $helper;
        $this->productCode = $productCode;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var MagentoOrder $magentoOrder */
        $magentoOrder = $observer->getData('data_object');

        if (!$this->helper->isPostNLOrder($magentoOrder)) {
            return;
        }

        $postnlOrder = $this->orderRepository->getByFieldWithValue('quote_id', $magentoOrder->getQuoteId());
        if (!$postnlOrder) {
            $postnlOrder = $this->orderRepository->create();
        }

        $this->setProductCode($postnlOrder, $magentoOrder);
        $postnlOrder->setData('order_id', $magentoOrder->getId());
        $postnlOrder->setData('quote_id', $magentoOrder->getQuoteId());
        $postnlOrder->setData('parcel_count', $this->parcelCount->get($magentoOrder));

        $this->orderRepository->save($postnlOrder);
    }

    /**
     * @param $postnlOrder
     * @param $magentoOrder
     */
    private function setProductCode(OrderInterface $postnlOrder, MagentoOrder $magentoOrder)
    {
        /**
         * If the product code is not set by the user then calculate it and save it also. It is possible that it is not
         * set because the deliveryoptions are disabled or this is an EPS shipment.
         */
        if (!$postnlOrder->getProductCode()) {
            $option          = $this->itemsToOption->get($magentoOrder->getItems());
            $shippingAddress = $magentoOrder->getShippingAddress();
            $country         = $shippingAddress->getCountryId();
            $productInfo     = $this->productCode->get('', $option, $country);
            $postnlOrder->setProductCode($productInfo['code']);
            $postnlOrder->setType($productInfo['type']);
        }
    }
}
