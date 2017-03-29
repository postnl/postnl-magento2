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

use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Pdf\Fpdf;
use TIG\PostNL\Helper\Pdf\Positions;
use TIG\PostNL\Test\TestCase;

class FpdfTest extends TestCase
{
    protected $instanceClass = Fpdf::class;

    /**
     * @return array
     */
    public function updatePageProvider()
    {
        return [
            ['A4', 2, 3],
            ['A4', Fpdf::MAX_LABELS_PER_PAGE, 1],
            ['A6', 1, 3]
        ];
    }

    public function testSetLabelCounter()
    {
        $randomLabelCounter = rand(0, 10);

        $instance = $this->getInstance();
        $instance->setLabelCounter($randomLabelCounter);
        $result = $instance->getLabelCounter();

        $this->assertEquals($randomLabelCounter, $result);
    }

    public function testIncreaseLabelCounter()
    {
        $randomLabelCounter = rand(0, 10);
        $expected = ($randomLabelCounter + 1);

        $instance = $this->getInstance();
        $instance->setLabelCounter($randomLabelCounter);
        $instance->increaseLabelCounter();
        $result = $instance->getLabelCounter();

        $this->assertEquals($expected, $result);
    }

    public function testResetLabelCounter()
    {
        $instance = $this->getInstance();
        $instance->resetLabelCounter();
        $result = $instance->getLabelCounter();

        $this->assertEquals(1, $result);
    }
}
