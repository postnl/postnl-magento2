<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Services\Import;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Import;

use TIG\PostNL\Model\Carrier\Tablerate;

/**
 * Class GetFreeBoxes
 *
 * @package TIG\PostNL\Services\Import
 */
class Csv
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var Tablerate
     */
    private $tablerate;

    /**
     * @param Filesystem $filesystem
     * @param Import     $import
     * @param Tablerate  $tablerate
     */
    public function __construct(
        Filesystem $filesystem,
        Import $import,
        Tablerate $tablerate
    ) {
        $this->filesystem = $filesystem;
        $this->import = $import;
        $this->tablerate = $tablerate;
    }

    /**
     * @param $filePath
     * @param $websiteId
     * @param $conditionName
     *
     * @return array
     * @throws LocalizedException
     */
    public function getData($filePath, $websiteId, $conditionName)
    {
        $file = $this->getCsvFile($filePath);
        $conditionFullName = $this->tablerate->getCode('condition_name_short', $conditionName);
        $importData = $this->import->getData($file, $websiteId, $conditionName, $conditionFullName);

        $columns = $this->import->getColumns();
        $records = [];

        foreach ($importData as $data) {
            $records[] = $data;
        }

        $file->close();

        $this->checkImportErrors();

        return [
            'columns' => $columns,
            'records' => $records
        ];
    }

    /**
     * @param string $filePath
     *
     * @return \Magento\Framework\Filesystem\File\ReadInterface
     */
    private function getCsvFile($filePath)
    {
        $tmpDirectory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        $path = $tmpDirectory->getRelativePath($filePath);
        return $tmpDirectory->openFile($path);
    }

    /**
     * @throws LocalizedException
     */
    private function checkImportErrors()
    {
        if ($this->import->hasErrors()) {
            // @codingStandardsIgnoreLine
            $error = __(
                'We couldn\'t import this file because of these errors: %1',
                implode(" \n", $this->import->getErrors())
            );

            throw new LocalizedException($error);
        }
    }
}
