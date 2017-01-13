<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Observer\Handlers;

use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Webservices\Endpoints\SentDate;

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
     * @param SentDate        $sentDate
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        SentDate $sentDate,
        OrderRepository $orderRepository
    ) {
        $this->sentDate = $sentDate;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Shipment $shipment
     *
     * @return mixed
     */
    public function get(Shipment $shipment)
    {
        $postnlOrder = $this->getPostnlOrder($shipment);

        $this->sentDate->setParameters($shipment, $postnlOrder);
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
        return $this->orderRepository->getByOrder($shipment->getOrder());
    }
}
