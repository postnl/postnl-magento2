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
namespace TIG\PostNL\Observer\TIGPostNLOrderSaveBefore;

use TIG\PostNL\Service\Order\ShipAt;
use TIG\PostNL\Service\Order\ProductCode;
use TIG\PostNL\Service\Order\FirstDeliveryDate;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetDefaultData implements ObserverInterface
{
    /**
     * @var ProductCode
     */
    private $productCode;

    /**
     * @var FirstDeliveryDate
     */
    private $firstDeliveryDate;

    /**
     * @var ShipAt
     */
    private $shipAt;

    /**
     * SetDefaultData constructor.
     *
     * @param ProductCode       $productCode
     * @param FirstDeliveryDate $firstDeliveryDate
     * @param ShipAt            $shipAt
     */
    public function __construct(
        ProductCode $productCode,
        FirstDeliveryDate $firstDeliveryDate,
        ShipAt $shipAt
    ) {
        $this->productCode = $productCode;
        $this->firstDeliveryDate = $firstDeliveryDate;
        $this->shipAt = $shipAt;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \TIG\PostNL\Api\Data\OrderInterface $order */
        $order = $observer->getData('data_object');

        if (!$order->getProductCode()) {
            $order->setProductCode($this->productCode->get());
        }

        if (!$order->getDeliveryDate()) {
            $this->firstDeliveryDate->set($order);
        }

        if (!$order->getShipAt()) {
            $this->shipAt->set($order);
        }
    }
}
