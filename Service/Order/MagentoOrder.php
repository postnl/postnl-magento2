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
use \Magento\Quote\Api\CartRepositoryInterface;

class MagentoOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * MagentoOrder constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository  = $cartRepository;
    }

    /**
     * @param $identifier
     * @param string $type
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|\Magento\Quote\Api\Data\CartInterface
     */
    public function get($identifier, $type = 'order' )
    {
        if ($type !== 'order') {
            return $this->cartRepository->get($identifier);
        }
        return $this->orderRepository->get($identifier);
    }

    /**
     * @param $identifier
     * @param string $type
     *
     * @return null|string
     */
    public function getCountry($identifier, $type = 'order')
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->get($identifier, $type);
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
