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
namespace TIG\PostNL\Test\Unit\Service\Handler;

use Magento\Framework\Phrase;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Test\TestCase;

class BarcodeHandlerTest extends TestCase
{
    protected $instanceClass = BarcodeHandler::class;

    /**
     * @return array
     */
    public function generateBarcodeProvider()
    {
        return [
            [
                (Object)['Barcode' => '3STOTA123457890'],
                '3STOTA123457890',
            ]
        ];
    }

    /**
     * @param $callReturnValue
     * @param $expected
     *
     * @dataProvider generateBarcodeProvider
     */
    public function testGenerate($callReturnValue, $expected)
    {
        $barcodeMock = $this->getBarcodeMock($callReturnValue);

        $instance = $this->getInstance(['barcodeEndpoint' => $barcodeMock]);
        $result = $this->invoke('generate', $instance);

        if ($result instanceof Phrase) {
            $result = $result->render();
        }

        $this->assertEquals($expected, $result);
    }

    public function testShouldThrowExceptionWhenInvalidResponse()
    {
        $message = 'Invalid GenerateBarcode response: \'Response by unittest\'';
        $barcodeMock = $this->getBarcodeMock('Response by unittest');

        try {
            $instance = $this->getInstance(['barcodeEndpoint' => $barcodeMock]);
            $result = $this->invoke('generate', $instance);
            if ($result instanceof Phrase) {
                $result->render();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->assertEquals($message, $exception->getMessage());
        }
    }

    /**
     * @param $callReturnValue
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getBarcodeMock($callReturnValue)
    {
        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call', 'setStoreId']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn($callReturnValue);

        $barcodeMock->expects($this->once())->method('setStoreId');

        return $barcodeMock;
    }
}
