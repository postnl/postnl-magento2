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
namespace TIG\PostNL\Service\Handler;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Model\Order;

class SentDateHandler
{
    /**
     * @var \TIG\PostNL\Model\ShipmentRepository
     */
    private $orderRepository;

    /**
     * @var SentDate
     */
    private $sentDate;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param SentDate          $sentDate
     * @param OrderRepository   $orderRepository
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        SentDate $sentDate,
        OrderRepository $orderRepository,
        TimezoneInterface $timezone
    ) {
        $this->sentDate = $sentDate;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
    }

    /**
     * @param Shipment $shipment
     *
     * @return mixed
     */
    public function get(Shipment $shipment)
    {
        /** @var  Order $postnlOrder */
        $postnlOrder = $this->getPostnlOrder($shipment);

        $this->sentDate->setParameters($shipment->getShippingAddress(), $shipment->getStoreId(), $postnlOrder);

        return $this->sentDate->call();
    }

    /**
     * @param Shipment $shipment
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPostnlOrder(Shipment $shipment)
    {
        return $this->orderRepository->getByFieldWithValue('order_id', $shipment->getOrderId());
    }
}
