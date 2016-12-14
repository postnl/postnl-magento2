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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Integration\Observer;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Observer\SalesOrderShipmentSaveAfterEvent;
use TIG\PostNL\Test\TestCase;

class TestSalesOrderShipmentSaveAfterEvent extends TestCase
{
    /**
     * @param array $args
     *
     * @return SalesOrderShipmentSaveAfterEvent
     */
    public function getInstance($args = [])
    {
        return $this->objectManager->getObject(SalesOrderShipmentSaveAfterEvent::class, $args);
    }

    public function testExecute()
    {
        $id = rand(1000, 2000);
        /** @var Observer $observer */
        $observer = $this->objectManager->getObject(Observer::class);
        $observer->setData('data_object');

        $shipmentMock = $this->getMock(Shipment::class, ['getId']);

        $getIdMock = $shipmentMock->expects($this->once());
        $getIdMock->method('getId');
        $getIdMock->willReturn($id);

        $this->getInstance()->execute($observer);
    }
}
