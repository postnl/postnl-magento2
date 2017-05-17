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
namespace TIG\PostNL\Unit\Block\Adminhtml\Renderer;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Block\Adminhtml\Renderer\ProductCode as Renderer;
use TIG\PostNL\Config\Source\Options\ProductOptions;

class ProductCodeTest extends TestCase
{
    protected $instanceClass = Renderer::class;

    public function getDataProvider()
    {
        return [
            'standard full'    => ['3085', true, 'Standard shipment', 'Standard shipment (3085)'],
            'standard small'   => ['3085', false, 'standard', 'Standard (3085)'],
            'pakjegemak full'  => ['3534', true, 'Post Office + Extra Cover', 'Post Office + Extra Cover (3534)'],
            'pakjegemak small' => ['3534', false, 'pakjegemak', 'Pakjegemak (3534)']
        ];
    }

    /**
     * @param $code
     * @param $small
     * @param $label
     * @param $expect
     *
     * @dataProvider getDataProvider
     */
    public function testRender($code, $small, $label, $expect)
    {
        $optionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $optionsExpects = $optionsMock->expects($this->once());
        $optionsExpects->method('getOptionLabel');
        $optionsExpects->with($code, $small);
        $optionsExpects->willReturn($label);

        $instance = $this->getInstance([
            'productOptions' => $optionsMock
        ]);

        $result = $this->invokeArgs('render', [$code, $small], $instance);

        $this->assertEquals($expect, $result);
    }
}
