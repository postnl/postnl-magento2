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
namespace TIG\PostNL\Plugin\Order;

use \TIG\PostNL\Model\OrderRepository;

class ShippingAddress
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * ShippingAddress constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Because Magento does not react to custom address types, we could not name the PG address type as 'pg_address'
     * If we did Magento would totaly ignore it when calling upon the shippingAddresses.
     *
     * This fix makes sure that the PG address is used if set to the order and exists.
     * (Contribution : Mark van der Werf)
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\Order\Address|null $result
     *
     * @return \Magento\Sales\Model\Order\Address|null|bool
     */
    public function afterGetShippingAddress($subject, $result)
    {
        if (!$subject->getId()) {
            return $result;
        }

        $postnlOrder = $this->orderRepository->getByOrderId($subject->getId());
        if (!$postnlOrder->getPgOrderAddressId()) {
            return $result;
        }

        $pgAddres = $subject->getAddressById($postnlOrder->getPgOrderAddressId());
        if (!$pgAddres) {
            return $result;
        }

        return $pgAddres;
    }
}
