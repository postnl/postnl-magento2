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
namespace TIG\PostNL\Test\Unit\Webservices\Endpoints;

use Magento\Sales\Model\Order\Address;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\SentDate;

class SentDateTest extends TestCase
{
    public $instanceClass = SentDate::class;

    public function theRightCountryIdIsReturnedProvider()
    {
        return [
            ['NL', 'NL'],
            ['BE', 'BE'],
            ['DE', 'NL'],
            ['UK', 'NL'],
            ['ES', 'NL'],
        ];
    }

    /**
     * @dataProvider theRightCountryIdIsReturnedProvider
     */
    public function testTheRightCountryIdIsReturned($shipTo, $expected)
    {
        /** @var Address $address */
        $address = $this->getObject(Address::class);
        $address->setCountryId($shipTo);

        $result = $this->invokeArgs('getCountryId', [$address]);

        $this->assertEquals($expected, $result);
    }
}
