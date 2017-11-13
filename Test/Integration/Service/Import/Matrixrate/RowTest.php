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

namespace TIG\PostNL\Test\Integration\Service\Import\Matrixrate;

use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Service\Import\Matrixrate\Row;

class RowTest extends TestCase
{
    public $instanceClass = Row::class;

    /**
     * @var Row
     */
    private $instance;

    /**
     * @var array
     */
    private $validRow = [];

    public function setUp()
    {
        parent::setUp();

        $this->instance = $this->getInstance();

        $this->validRow = [
            'US',           // country
            'California',   // region/stats
            '12345',        // zip code
            '13.37',        // weight
            '159.99',       // subtotal
            '5',            // quantity
            'regular',      // regular
            '6.20',         // shipping price
        ];
    }

    private function convertPhraseArrayToTextArray($list)
    {
        return array_map(function (\Magento\Framework\Phrase $item) {
            return $item->render();
        }, $list);
    }

    private function verifyError($expectedError)
    {
        $this->assertFalse($this->instance->process(1, $this->validRow));
        $this->assertTrue($this->instance->hasErrors());
        $errors = $this->convertPhraseArrayToTextArray($this->instance->getErrors());
        $this->assertContains($expectedError, $errors);
    }

    public function testAnInvalidCountry()
    {
        $this->validRow[0] = 'AA';
        $this->verifyError('Invalid country "AA" in row #1.');
    }

    public function testAnInvalidRegion()
    {
        $this->validRow[1] = 'non-existing';
        $this->verifyError('Invalid region/state "non-existing" in row #1.');
    }

    public function testAnInvalidWeight()
    {
        $this->validRow[3] = 'invalid';
        $this->verifyError('Invalid weight "invalid" in row #1.');
    }

    public function testAnInvalidSubtotal()
    {
        $this->validRow[4] = 'invalid';
        $this->verifyError('Invalid subtotal "invalid" in row #1.');
    }

    public function testAnInvalidQuantity()
    {
        $this->validRow[5] = 'invalid';
        $this->verifyError('Invalid quantity "invalid" in row #1.');
    }

    public function testAnInvalidParcelType()
    {
        $this->validRow[6] = 'invalid';
        $types = implode(', ', Row::ALLOWED_PARCEL_TYPES);
        $this->verifyError('Invalid parcel type "invalid" in row #1. Valid values are: "' . $types . '".');
    }

    public function testAnInvalidShippingPrice()
    {
        $this->validRow[7] = 'invalid';
        $this->verifyError('Invalid shipping price "invalid" in row #1.');
    }

    public function testAnDuplicateRow()
    {
        $this->validRow[2] = '*';
        $this->assertTrue(is_array($this->instance->process(1, $this->validRow)));
        $this->verifyError('Duplicate row #1 (country "US", region/state "California", zip "*", weight "13.37", ' .
            'subtotal "159.99", quantity "5" and parcel type "regular").');
    }

    public function testReturnsTheDataInTheCorrectFormat()
    {
        $expected = [
            'website_id' => 1,
            'destiny_country_id' => 'US',
            'destiny_region_id' => 12,
            'destiny_zip_code' => '12345',
            'weight' => 13.37,
            'subtotal' => 159.99,
            'quantity' => 5,
            'parcel_type' => 'regular',
            'price' => 6.20,
        ];

        $this->assertEquals($expected, $this->instance->process(1, $this->validRow));
        $this->assertFalse($this->instance->hasErrors());
    }
}
