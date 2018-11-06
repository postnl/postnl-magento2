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

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Order\ProductCodeAndType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order as MagentoOrder;
use TIG\PostNL\Model\Order;
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

        if (!$this->helper->isPostNLOrder($magentoOrder) || !$magentoOrder->getId()) {
            return;
        }

        $postnlOrder = $this->getPostNLOrder($magentoOrder);
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
     * Before 1.3.0 the quote ID was in FK relation, but when an quote is deleted by the Cron of Magento the quote ID
     * of the PostNL order was set to NULL and the Magento order keeps the old quote ID.
     *
     * So when parsing the Magento->getQuoteId() the PostNLorder could have NULL as quote_id, but the order is still in
     * process. And when saving the order it would give the sql error :
     *  - Cannot add or update a child row: a foreign key constraint fails
     *
     * @param MagentoOrder $magentoOrder
     *
     * @return null|\TIG\PostNL\Model\AbstractModel|\TIG\PostNL\Model\Order
     * @throws LocalizedException
     */
    private function getPostNLOrder(MagentoOrder $magentoOrder)
    {
        $postnlOrder = $this->orderRepository->getByOrderId($magentoOrder->getId());
        if (!$postnlOrder) {
            $postnlOrder = $this->orderRepository->getByQuoteWhereOrderIdIsNull($magentoOrder->getQuoteId());
        }

        if (!$postnlOrder) {
            return null;
        }

        if ($postnlOrder->getOrderId() == null) {
            return $postnlOrder;
        }

        if ($magentoOrder->getId() == $postnlOrder->getOrderId()) {
            return $postnlOrder;
        }

        return $this->returnNewRecord($postnlOrder);
    }

    /**
     * When the quote has more than one Magento Order, it could be that one is canceled. So when this canceled order
     * gets an update from the PSP it will update the incorrect PostNL record because of the same quote ID. Thats why
     * we will create a new record.
     *
     * @param Order $postnlOrder
     *
     * @return Order
     */
    private function returnNewRecord(Order $postnlOrder)
    {
        $newRecord = $this->orderRepository->create();
        $newRecord->setData($postnlOrder->getData());

        return $newRecord;
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
