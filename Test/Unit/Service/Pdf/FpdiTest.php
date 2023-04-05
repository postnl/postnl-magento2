<?php

namespace TIG\PostNL\Test\Unit\Service\Pdf;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Test\TestCase;

class FpdiTest extends TestCase
{
    public $instanceClass = Fpdi::class;

    /**
     * @return array
     */
    public function pixelsToPointsProvider()
    {
        return [
            'no pixels' => [0, 0],
            'single digit pixel' => [3, 0.8],
            'multi digit pixel' => [123, 32.4],
            'single decimal pixel' => [45.6, 12],
            'multi decimal pixel' => [78.901, 20.8]
        ];
    }

    /**
     * @param $pixels
     * @param $expected
     *
     * @dataProvider pixelsToPointsProvider
     */
    public function testPixelsToPoints($pixels, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->pixelsToPoints($pixels);
        $this->assertEquals($result, $expected);
    }
}
