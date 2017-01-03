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
namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Model\ShipmentBarcodeRepository;
use TIG\PostNL\Test\TestCase;

class ShipmentBarcodeRepositoryTest extends TestCase
{
    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance($args = [])
    {
        return $this->objectManager->getObject(ShipmentBarcodeRepository::class, $args);
    }

    public function testSave()
    {
        $shipmentBarcodeFactoryMock = $this->getMock(
            '\TIG\PostNL\Model\ShipmentBarcodeInterface',
            ['getIdentities', 'save']
        );

        $saveExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $instance = $this->getInstance();
        $result = $instance->save($shipmentBarcodeFactoryMock);

        $this->assertEquals($shipmentBarcodeFactoryMock, $result);
    }

    public function testgetById()
    {
        $id = rand(1000, 2000);

        $shipmentBarcodeFactoryMock = $this->getMock(
            '\TIG\PostNL\Model\ShipmentBarcodeFactory',
            ['create', 'load', 'getId']
        );

        $createExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $loadExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $loadExpects->method('load');
        $loadExpects->willReturnSelf();

        $getIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $getIdExpects->method('getId');
        $getIdExpects->willReturn($id);

        $instance = $this->getInstance(['objectFactory' => $shipmentBarcodeFactoryMock]);
        $result = $instance->getById($id);

        $this->assertInstanceOf('\TIG\PostNL\Model\ShipmentBarcodeFactory', $result);
        $this->assertEquals($shipmentBarcodeFactoryMock, $result);
    }

    public function testDelete()
    {
        $shipmentBarcodeFactoryMock = $this->getMock(
            '\TIG\PostNL\Model\ShipmentBarcodeInterface',
            ['getIdentities', 'delete']
        );

        $saveExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $saveExpects->method('delete');

        $instance = $this->getInstance();
        $result = $instance->delete($shipmentBarcodeFactoryMock);

        $this->assertTrue($result);
    }
}
