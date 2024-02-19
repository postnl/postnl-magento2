<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Service\Import\Matrixrate\Data;
use TIG\PostNL\Test\Integration\TestCase;

class MatrixrateTest extends TestCase
{
    public $instanceClass = Matrixrate::class;

    /**
     * @var RateRequest $request
     */
    private $request;

    public function setUp() : void
    {
        parent::setUp();

        $this->request = $this->getObject(RateRequest::class);
        $this->request->setWebsiteId(1);
        $this->request->setDestCountryId('NL');
        $this->request->setDestRegionId(0);
        $this->request->setDestPostcode('1234');
        $this->request->setPackageWeight(1000);
        $this->request->setPackageValue(100);
        $this->request->setPackageQty(2);
    }

    private function importFile($file)
    {
        $file = $this->loadFile('Fixtures/Matrixrate/' . $file . '.csv');
        $this->getObject(Data::class)->import($file);
        $file->close();
    }

    public function returnsTheCorrectRateForCountryProvider()
    {
        return [
            'dutch, normal'                 => ['NL', 0, ['price' => 5]],
            'multicountry1'                 => ['FR', 0, ['price' => 10]],
            'multicountry2'                 => ['MC', 0, ['price' => 10]],
            'Spain, Las Palmas'             => ['ES', 157, ['price' => 15]],
            'Spain, Santa Cruz de Tenerife' => ['ES', 170, ['price' => 20]],
        ];
    }

    /**
     * @param $country
     * @param $regionId
     * @param $expected
     *
     * @dataProvider returnsTheCorrectRateForCountryProvider
     */
    public function testReturnsTheCorrectRateForCountry($country, $regionId, $expected)
    {
        $this->importFile('pricing');
        /** @var RateRequest $request */
        $this->request->setDestCountryId($country);
        $this->request->setDestRegionId($regionId);

        $collection = $this->getObject(\TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection::class);
        $result = $this->getInstance(['matrixrateCollection' => $collection])->getRate($this->request, 'regular');

        $this->assertEquals($expected['price'], $result['price']);
    }

    public function testAnEmptyParcelTypeIsConvertedToRegular()
    {
        $this->importFile('parceltypes');

        $collection = $this->getObject(\TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection::class);
        $result = $this->getInstance(['matrixrateCollection' => $collection])->getRate($this->request, '');

        $this->assertEquals(5, $result['price']);
    }
}
