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

use Magento\Framework\Filesystem;
use TIG\PostNL\Model\Carrier\ResourceModel\MatrixRate\Collection as MatrixrateCollection;
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

    public function setUp()
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
            $lines = explode("\n", $contents);

            $output[] = [$filePath, count($lines)];
        }

        return $output;
    }

    /**
     * @dataProvider importCsvFilesProvider
     *
     * @param $filePath
     * @param $count
     * @return Filesystem\File\ReadInterface
     */
    public function testImportCsvFiles($filePath, $count)
    {
        $path = $this->directory->getRelativePath($filePath);
        $file = $this->directory->openFile($path);

        /** @var Data $instance */
        $instance = $this->getInstance();

        $instance->import($file);

        /** @var MatrixrateCollection $collection */
        $collection = $this->getObject(MatrixrateCollection::class);

        $result = $collection->getSize();
        $this->assertEquals($count, $result);
    }

    public function testAnImportWithoutHeaders()
    {
        $path = realpath(__DIR__ . '/../../../../Fixtures/Matrixrate/incorrectformat.csv');

        $path = $this->directory->getRelativePath($path);
        $file = $this->directory->openFile($path);

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
}
