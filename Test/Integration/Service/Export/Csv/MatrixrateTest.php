<?php

namespace TIG\PostNL\Service\Export\Csv;

use Magento\Framework\Filesystem;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;
use TIG\PostNL\Service\Wrapper\StoreInterface;
use TIG\PostNL\Test\Integration\TestCase;

class MatrixrateTest extends TestCase
{
    public $instanceClass = Matrixrate::class;

    /**
     * @var \TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection
     */
    private $collection;

    /**
     * @var Matrixrate
     */
    private $instance;

    public function setUp() : void
    {
        parent::setUp();

        // Import the default rates to have sample data
        $fixture = $this->loadFile('Fixtures/Matrixrate/default_rates.csv');
        $this->getObject(\TIG\PostNL\Service\Import\Matrixrate\Data::class)->import($fixture);
        $fixture->close();

        /** @var StoreInterface $storeManager */
        $storeManager = $this->getObject(StoreInterface::class);

        /** @var MatrixrateRepositoryInterface $repository */
        $repository = $this->getObject(MatrixrateRepositoryInterface::class);

        $this->collection = $repository->getByWebsiteId($storeManager->getWebsiteId());
        $this->instance = $this->getInstance();
    }

    public function testHasTheCorrectHeaders()
    {
        $result = $this->instance->build($this->collection);

        $lines  = explode("\n", $result);
        $header = array_shift($lines);

        $this->assertStringContainsString('Country', $header);
        $this->assertStringContainsString('Province/state', $header);
        $this->assertStringContainsString('Zipcode', $header);
        $this->assertStringContainsString('Weight (and higher)', $header);
        $this->assertStringContainsString('Shipping price (and higher)', $header);
        $this->assertStringContainsString('Amount (and higher)', $header);
        $this->assertStringContainsString('Parcel type', $header);
        $this->assertStringContainsString('price', $header);
        $this->assertStringContainsString('Instructions', $header);
    }

    public function testHasTheCorrectSize()
    {
        $result = $this->instance->build($this->collection);

        $lines = explode("\n", trim($result));
        $this->assertEquals($this->getCollectionSize() + 1, count($lines));
    }

    public function testTheRegionIsNotAnId()
    {
        $result = $this->instance->build($this->collection);

        $lines  = explode(PHP_EOL, $result);
        array_shift($lines);
        foreach ($lines as $line) {
            $data = str_getcsv($line);

            $region = $data[1];
            if ($region == '*') {
                continue;
            }

            $this->assertFalse(is_numeric($region));
            $this->assertGreaterThanOrEqual(2, strlen($region));
        }
    }

    public function testTheCountryIsNotAnId()
    {
        $result = $this->instance->build($this->collection);

        $lines  = explode(PHP_EOL, $result);
        array_shift($lines);
        foreach ($lines as $line) {
            $data = str_getcsv($line);

            $countries = $data[0];
            if ($countries == '*') {
                continue;
            }

            $this->assertFalse(is_numeric($countries));
            foreach (explode(',', $countries) as $country) {
                $this->assertEquals(2, strlen($country));
            }
        }
    }

    public function testCanImportAnExport()
    {
        $collectionSizeBefore = $this->getCollectionSize();

        $result = $this->instance->build($this->collection);
        $filename = 'matrixrate.csv';

        /** @var Filesystem $filesystem */
        $filesystem   = $this->getObject(Filesystem::class);
        $tmpDirectory = $filesystem->getDirectoryRead(Filesystem\DirectoryList::SYS_TMP);

        $relativePath = $tmpDirectory->getRelativePath($filename);
        $path = $tmpDirectory->getAbsolutePath($filename);
        file_put_contents($path, $result);

        $file = $tmpDirectory->openFile($relativePath);

        /** @var \TIG\PostNL\Service\Import\Matrixrate\Data $import */
        $this->getObject(\TIG\PostNL\Service\Import\Matrixrate\Data::class)->import($file);

        $this->assertEquals($collectionSizeBefore, $this->getCollectionSize());
    }

    /**
     * @return int
     */
    private function getCollectionSize(): int
    {
        $collection = $this->getObject(\TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection::class);

        return $collection->getSize();
    }
}
