<?php

namespace TIG\PostNL\Test\Integration\Service\Import\Matrixrate;

use Magento\Framework\Filesystem;
use TIG\PostNL\Service\Import\Matrixrate\Data;
use TIG\PostNL\Test\Integration\Service\Import\IncorrectFormat;
use TIG\PostNL\Test\Integration\TestCase;

class DataTest extends TestCase
{
    public $instanceClass = Data::class;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $directory;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Filesystem $filesystem */
        $filesystem   = $this->getObject(Filesystem::class);
        $this->directory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
    }

    public function importCsvFilesProvider()
    {
        $output       = [];
        $fixturesPath = realpath(__DIR__ . '/../../../../Fixtures/Matrixrate/Types');
        $files        = scandir($fixturesPath);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $filePath = $fixturesPath . '/' . $file;

            $contents = file_get_contents($filePath);
            $contents = trim($contents);
            $lines = explode("\n", $contents);

            // -1 for the header
            $output[] = [$file, count($lines) - 1];
        }

        return $output;
    }

    /**
     * @dataProvider importCsvFilesProvider
     *
     * @param $filePath
     * @param $expected
     *
     * @return Filesystem\File\ReadInterface
     */
    public function testImportCsvFiles($filePath, $expected)
    {
        $file = $this->loadFile('Fixtures/Matrixrate/Types/' . $filePath);

        /** @var Data $instance */
        $instance = $this->getInstance();

        $instance->import($file);

        $this->assertEquals($expected, $this->getCollectionSize());
    }

    public function testAnImportWithoutHeaders()
    {
        $file = $this->loadFile('Fixtures/Matrixrate/incorrectformat.csv');

        /** @var Data $instance */
        $instance = $this->getInstance();

        try {
            $instance->import($file);
        } catch (IncorrectFormat $exception) {
            $this->assertEquals('[POSTNL-0194] Invalid PostNL Matrix Rates File Format', $exception->getMessage());
            $this->assertEquals('POSTNL-0194', $exception->getCode());
            return;
        }

        $this->fail('We expected an IncorrectFormat exception but got none.');
    }

    public function testPreviousDataGetsDeleted()
    {
        $file = $this->loadFile('Fixtures/Matrixrate/Types/regular.csv');

        /** @var Data $instance */
        $instance = $this->getInstance();

        $instance->import($file);
        $file->close();

        $firstSize = $this->getCollectionSize();

        $file = $this->loadFile('Fixtures/Matrixrate/Types/regular.csv');
        $instance->import($file);
        $file->close();

        $secondSize = $this->getCollectionSize();

        $this->assertEquals($firstSize, $secondSize);
    }

    public function testCanImportTheDefaultMatrixRates()
    {
        $file = $this->loadFile('Fixtures/Matrixrate/default_rates.csv');

        $this->getInstance()->import($file);
        $file->close();

        $this->assertEquals(1112, $this->getCollectionSize());
    }

    public function testAnEmptyRowGetsSkipped()
    {
        $file = $this->loadFile('Fixtures/Matrixrate/empty_row.csv');

        $this->getInstance()->import($file);
        $file->close();

        $this->assertEquals(2, $this->getCollectionSize());
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
