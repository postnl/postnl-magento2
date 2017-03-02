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
namespace TIG\PostNL\Test\Unit\Helper\Pdf;

use TIG\PostNL\Helper\Pdf\Positions;
use TIG\PostNL\Model\ShipmentLabel;
use TIG\PostNL\Test\TestCase;

class PositionsTest extends TestCase
{
    protected $instanceClass = Positions::class;

    /**
     * @return array
     */
    public function getProvider()
    {
        return [
            'empty_result' => [0, 0, null, 0],
            'positions_found' => [25, 25, ShipmentLabel::BARCODE_TYPE_LABEL, 4]
        ];
    }

    /**
     * @param $width
     * @param $height
     * @param $labelType
     * @param $expected
     *
     * @dataProvider getProvider
     */
    public function testGet($width, $height, $labelType, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->get($width, $height, $labelType);

        $this->assertCount($expected, $result);
    }

    /**
     * @return array
     */
    public function getForPositionProvider()
    {
        return [
            'empty_result' => [0, 0, 0, null, 0],
            'positions_found' => [25, 25, 2, ShipmentLabel::BARCODE_TYPE_LABEL, 3]
        ];
    }

    /**
     * @param $width
     * @param $height
     * @param $position
     * @param $labelType
     * @param $expected
     *
     * @dataProvider getForPositionProvider
     */
    public function testGetForPosition($width, $height, $position, $labelType, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->getForPosition($width, $height, $position, $labelType);

        $this->assertCount($expected, $result);

        if ($expected > 0) {
            $this->assertArrayHasKey('x', $result);
            $this->assertArrayHasKey('y', $result);
            $this->assertArrayHasKey('w', $result);
        } else {
            $this->assertArrayNotHasKey('x', $result);
            $this->assertArrayNotHasKey('y', $result);
            $this->assertArrayNotHasKey('w', $result);
        }
    }

    /**
     * @return array
     */
    public function getFourLabelsPerPageProvider()
    {
        return [
            [
                25,
                25,
                [
                    1 => ['x' => 16.4, 'y' => Positions::LABEL_BORDER_MARGIN, 'w' => 141.6],
                    2 => ['x' => 16.4, 'y' => 16.4, 'w' => 141.6],
                    3 => ['x' => Positions::LABEL_BORDER_MARGIN, 'y' => Positions::LABEL_BORDER_MARGIN, 'w' => 141.6],
                    4 => ['x' => Positions::LABEL_BORDER_MARGIN, 'y' => 16.4, 'w' => 141.6]
                ]
            ],
            [
                42,
                38,
                [
                    1 => ['x' => 24.9, 'y' => Positions::LABEL_BORDER_MARGIN, 'w' => 141.6],
                    2 => ['x' => 24.9, 'y' => 22.9, 'w' => 141.6],
                    3 => ['x' => Positions::LABEL_BORDER_MARGIN, 'y' => Positions::LABEL_BORDER_MARGIN, 'w' => 141.6],
                    4 => ['x' => Positions::LABEL_BORDER_MARGIN, 'y' => 22.9, 'w' => 141.6]
                ]
            ],
        ];
    }

    /**
     * @param $width
     * @param $height
     * @param $expected
     *
     * @dataProvider getFourLabelsPerPageProvider
     */
    public function testGetFourLabelsPerPage($width, $height, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->getFourLabelsPerPage($width, $height);

        $this->assertEquals($expected, $result);
    }
}
