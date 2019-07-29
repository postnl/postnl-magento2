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

namespace PostNL\Test\Unit\Service\Shipment\Label\Type;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Shipment\Label\Type\EPS;

class EPSTest extends TestCase
{
    /** @var EPS $instanceClass */
    public $instanceClass = EPS::class;
    
    /**
     * @throws \Exception
     */
    public function testIsPriorityProduct()
    {
        /** @var EPS $instance */
        $instance = $this->getInstance();
        $result   = $instance->isPriorityProduct(6350);
        $expected = true;
    
        $this->assertEquals($expected, $result);
    }
    
    /**
     * @throws \Exception
     */
    public function testIsRotatedProduct()
    {
        /** @var EPS $instance */
        $instance = $this->getInstance();
        $result   = $instance->isRotatedProduct(4940);
        $expected = true;
    
        $this->assertEquals($expected, $result);
    }
}
