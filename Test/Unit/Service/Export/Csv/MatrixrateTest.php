<?php

namespace TIG\PostNL\Unit\Service\Export\Csv;

use TIG\PostNL\Api\Data\MatrixrateInterface;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;
use TIG\PostNL\Service\Converter\IdToRegion;
use TIG\PostNL\Service\Converter\ZeroToStar;
use TIG\PostNL\Service\Export\Csv\Matrixrate;
use TIG\PostNL\Test\TestCase;

class MatrixrateTest extends TestCase
{
    protected $instanceClass = Matrixrate::class;

    public function testBuildWritesTheMatrixRatesAsCsv()
    {
        $idToRegion = $this->createStub(IdToRegion::class);
        $idToRegion->method('convert')->willReturnArgument(0);

        $zeroToStar = $this->createStub(ZeroToStar::class);
        $zeroToStar->method('convert')->willReturnArgument(0);

        $model = $this->createStub(MatrixrateInterface::class);
        $model->method('getDestinyCountryId')->willReturn('NL');
        $model->method('getDestinyRegionId')->willReturn('123');
        $model->method('getDestinyZipCode')->willReturn('1234AB');
        $model->method('getWeight')->willReturn('1.0');
        $model->method('getSubtotal')->willReturn('100.0');
        $model->method('getQuantity')->willReturn('2');
        $model->method('getParcelType')->willReturn('standard');
        $model->method('getPrice')->willReturn('5.5');

        $collection = $this->createStub(Collection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$model]));

        $instance = $this->getInstance([
            'idToRegion' => $idToRegion,
            'zeroToStar' => $zeroToStar,
        ]);

        $result = $instance->build($collection);

        $expected = 'Country,Province/state,Zipcode,"Weight (and higher)","Shipping price (and higher)",'
            . '"Amount (and higher)","Parcel type",price,Instructions' . "\n"
            . 'NL,123,1234AB,1.0,100.0,2,standard,5.5,';

        $this->assertSame($expected, $result);
    }
}
