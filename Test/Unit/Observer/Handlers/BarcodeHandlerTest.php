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
namespace TIG\PostNL\Test\Unit\Observer\Handlers;

use Magento\Framework\Phrase;
use TIG\PostNL\Observer\Handlers\BarcodeHandler;
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
                '3STOTA123457890'
            ],
            [
                'Response by unittest',
                'Invalid GenerateBarcode response: \'Response by unittest\''
            ],
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
        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn($callReturnValue);

        $instance = $this->getInstance(['barcodeEndpoint' => $barcodeMock]);
        $result = $instance->generate();

        if ($result instanceof Phrase) {
            $result = $result->render();
        }

        $this->assertEquals($expected, $result);
    }
}
