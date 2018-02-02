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
namespace TIG\PostNL\Service\Order;

use \Magento\Sales\Api\OrderRepositoryInterface;

class MagentoOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * MagentoOrder constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $postnlOrderId
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function get($postnlOrderId)
    {
        return $this->orderRepository->get($postnlOrderId);
    }

    /**
     * @param $postnlOrderId
     *
     * @return null|string
     */
    public function getCountry($postnlOrderId)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order   = $this->get($postnlOrderId);
        if (!$order) {
            return null;
        }

        $address = $order->getShippingAddress();
        if (!$address) {
            return null;
        }

        return $address->getCountryId();
    }
}
