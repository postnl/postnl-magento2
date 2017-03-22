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

use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Shipment\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    public $instanceClass = ProductOptions::class;

    public function returnsTheCorrectDataProvider()
    {
        return [
            ['pge', ['Characteristic' => '118', 'Option' => '002']],
            ['evening', ['Characteristic' => '118', 'Option' => '006']],
            ['sunday', ['Characteristic' => '101', 'Option' => '008']],
        ];
    }

    /**
     * @dataProvider returnsTheCorrectDataProvider
     *
     * @param $type
     * @param $expected
     *
     * @throws \Exception
     */
    public function testReturnsTheCorrectData($type, $expected)
    {
        /** @var Shipment $shipment */
        $shipment = $this->getObject(Shipment::class);
        $shipment->setShipmentType($type);

        /** @var ProductOptions $instance */
        $instance = $this->getInstance();
        $response = $instance->get($shipment);
        $result = $response['ProductOption'];

        foreach ($expected as $type => $value) {
            $this->assertEquals($result[$type], $value);
            unset($result[$type]);
        }

        if (count($result)) {
            $this->fail('$result contains values but should be empty');
        }
    }
}
