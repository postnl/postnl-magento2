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
namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    public $instanceClass = ProductOptions::class;

    public function returnsTheCorrectDataProvider()
    {
        return [
            ['pge', false, false, ['Characteristic' => '118', 'Option' => '002']],
            ['evening', false, false, ['Characteristic' => '118', 'Option' => '006']],
            ['sunday', false, false, ['Characteristic' => '101', 'Option' => '008']],
            ['pge', true, false, ['Characteristic' => '118', 'Option' => '002']],
            ['evening', true, false, ['Characteristic' => '118', 'Option' => '006']],
            ['sunday', true, false, ['Characteristic' => '101', 'Option' => '008']],
        ];
    }

    /**
     * @dataProvider returnsTheCorrectDataProvider
     *
     * @param $type
     * @param $flat
     * @param $isIdCheck
     * @param $expected
     *
     * @throws \Exception
     */
    public function testReturnsTheCorrectData($type, $flat, $isIdCheck, $expected)
    {
        /** @var ShipmentInterface|\PHPUnit\Framework\MockObject\MockObject$shipmentMock */
        $shipmentMock = $this->getFakeMock(ShipmentInterface::class)->getMock();
        $shipmentMock->method('getShipmentType')->willReturn($type);
        $shipmentMock->method('isIDCheck')->willReturn($isIdCheck);

        /** @var ProductOptions $instance */
        $instance = $this->getInstance();

        $result = $instance->get($shipmentMock, $flat);
        if (!$flat) {
            $result = $result['ProductOption'];
        }

        foreach ($expected as $type => $value) {
            $this->assertEquals($result[$type], $value);
            unset($result[$type]);
        }

        if (count($result)) {
            $this->fail('$result contains values but should be empty');
        }
    }
}
