<?php

namespace TIG\PostNL\Test\Unit\Service\Carrier\Price\Filter;

use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Carrier\Price\Filter\CountryFilter;

class CountryFilterTest extends TestCase
{
    public $instanceClass = CountryFilter::class;

    /**
     * @var array
     */
    private $input = [];

    protected function setUp() : void
    {
        parent::setUp();

        $this->input = [
            'NL, no region'      => ['destiny_country_id' => 'NL', 'destiny_region_id' => 0, 'destiny_zip_code' => '*'],
            'NL, with postcode'  => ['destiny_country_id' => 'NL', 'destiny_region_id' => 0, 'destiny_zip_code' => '1014 BA'],
            'ES, without region' => ['destiny_country_id' => 'ES', 'destiny_region_id' => 0, 'destiny_zip_code' => '*'],
            'ES with region'     => ['destiny_country_id' => 'ES', 'destiny_region_id' => 157, 'destiny_zip_code' => '*'],
            'ES with region 2'   => ['destiny_country_id' => 'ES', 'destiny_region_id' => 170, 'destiny_zip_code' => '*'],
            'wildcard country'   => ['destiny_country_id' => 0, 'destiny_region_id' => 170, 'destiny_zip_code' => '*'],
            'all wildcards'      => ['destiny_country_id' => 0, 'destiny_region_id' => 0, 'destiny_zip_code' => '*'],
        ];
    }

    public function testFilterByCountry()
    {
        $request = $this->getObject(RateRequest::class);
        $request->setDestCountryId('NL');
        $request->setDestRegionId(0);

        $result = $this->getInstance()->filter($request, $this->input);
        $result = array_values($result);

        $this->assertCount(2, $result);
        $this->assertEquals('NL', $result[0]['destiny_country_id']);
        $this->assertEquals('*', $result[0]['destiny_zip_code']);
        $this->assertEquals(0, $result[1]['destiny_country_id']);
        $this->assertEquals('*', $result[1]['destiny_zip_code']);
    }

    public function testFilterByCountryAndRegionId()
    {
        $request = $this->getObject(RateRequest::class);
        $request->setDestCountryId('ES');
        $request->setDestRegionId('157');

        $result = $this->getInstance()->filter($request, $this->input);
        $result = array_values($result);

        $this->assertCount(3, $result);
        $this->assertEquals('ES', $result[0]['destiny_country_id']);
        $this->assertEquals(0, $result[0]['destiny_region_id']);
        $this->assertEquals('ES', $result[1]['destiny_country_id']);
        $this->assertEquals(157, $result[1]['destiny_region_id']);
        $this->assertEquals(0, $result[2]['destiny_country_id']);
        $this->assertEquals(0, $result[2]['destiny_region_id']);
    }

    public function testFilterByRegionWithAnWildcardCountry()
    {
        $request = $this->getObject(RateRequest::class);
        $request->setDestCountryId('ES');
        $request->setDestRegionId('170');

        $result = $this->getInstance()->filter($request, $this->input);
        $result = array_values($result);

        $this->assertCount(4, $result);
        $this->assertEquals('ES', $result[0]['destiny_country_id']);
        $this->assertEquals(0, $result[0]['destiny_region_id']);
        $this->assertEquals('ES', $result[1]['destiny_country_id']);
        $this->assertEquals(170, $result[1]['destiny_region_id']);
        $this->assertEquals(170, $result[2]['destiny_region_id']);
        $this->assertEquals(0, $result[3]['destiny_country_id']);
        $this->assertEquals(0, $result[3]['destiny_region_id']);
    }

    public function testFilterByPostcode()
    {
        $request = $this->getObject(RateRequest::class);
        $request->setDestCountryId('NL');
        $request->setDestRegionId('0');
        $request->setDestPostcode('1014 BA');

        $result = $this->getInstance()->filter($request, $this->input);
        $result = array_values($result);

        $this->assertCount(3, $result);
        $this->assertEquals('NL', $result[0]['destiny_country_id']);
        $this->assertEquals('*', $result[0]['destiny_zip_code']);
        $this->assertEquals('NL', $result[1]['destiny_country_id']);
        $this->assertEquals('1014 BA', $result[1]['destiny_zip_code']);
        $this->assertEquals(0, $result[2]['destiny_country_id']);
        $this->assertEquals('*', $result[2]['destiny_zip_code']);
    }
}
