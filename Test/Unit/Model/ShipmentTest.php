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
namespace TIG\PostNL\Test\Unit\Model;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Test\TestCase;

class ShipmentTest extends TestCase
{
    public $instanceClass = Shipment::class;

    public function isExtraCoverProvider()
    {
        return [
            [3085, false],
            [3544, true],
        ];
    }

    /**
     * @param $productCode
     * @param $expected
     *
     * @dataProvider isExtraCoverProvider
     */
    public function testIsExtraCover($productCode, $expected)
    {
        $functionResponse = [
            'isExtraCover' => $expected,
        ];

        $productCodeMock = $this->getFakeMock(ProductOptions::class, true);
        $this->mockFunction($productCodeMock, 'getOptionsByCode', $functionResponse, $productCode);

        /** @var Shipment $instance */
        $instance = $this->getInstance(['productOptions' => $productCodeMock]);
        $instance->setProductCode($productCode);

        $result = $instance->isExtraCover();

        $this->assertEquals($expected, $result);
    }
}
