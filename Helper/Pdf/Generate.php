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
namespace TIG\PostNL\Helper\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Shipping\Model\Shipping\LabelGenerator;

/**
 * Class Generate
 *
 * @package TIG\PostNL\Helper\Pdf
 */
class Generate
{
    const TEMP_LABEL_FOLDER = 'log' . DIRECTORY_SEPARATOR . 'PostNL' . DIRECTORY_SEPARATOR . 'templabel';
    const TEMP_LABEL_FILENAME = 'TIG_PostNL_temp.pdf';

    /** @var array $tempFilesSaved */
    protected $tempFilesSaved = [];

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var LabelGenerator
     */
    private $labelGenerator;
    /**
     * @var File
     */
    private $ioFile;

    /**
     * @param File           $ioFile
     * @param DirectoryList  $directoryList
     * @param LabelGenerator $labelGenerator
     */
    public function __construct(
        File $ioFile,
        DirectoryList $directoryList,
        LabelGenerator $labelGenerator
    ) {
        $this->directoryList = $directoryList;
        $this->labelGenerator = $labelGenerator;
        $this->ioFile = $ioFile;
    }

    public function get($labels)
    {
        if (!is_array($labels)) {
            $labels = array($labels);
        }

        if (!class_exists('FPDI')) {
            return $this->getZendPdf($labels);
        }

        $pdf = new \FPDI();
        $pdf->SetTitle('PostNL Shipping Labels');
        $pdf->SetAuthor('PostNL');
        $pdf->SetCreator('PostNL');

        /** 1 => array(
        'x' => 152.4,
        'y' => 3.9,
        'w' => 141.6,
        ),
        2 => array(
        'x' => 152.4,
        'y' => 108.9,
        'w' => 141.6,
        ),
        3 => array(
        'x' => 3.9,
        'y' => 3.9,
        'w' => 141.6,
        ),
        4 => array(
        'x' => 3.9,
        'y' => 108.9,
        'w' => 141.6,
        ), */

        foreach ($labels as $label) {
            $tempLabelFile = $this->saveTempLabel($label);

            $pdf->AddPage('L', 'A4');
            $position = [
                'x' => 152.4,
                'y' => 3.9,
                'w' => 141.6,
            ];

            $pdf->setSourceFile($tempLabelFile);
            $templateIndex = $pdf->importPage(1);
            $pdf->useTemplate($templateIndex, $position['x'], $position['y'], $position['w']);
        }

        $this->deleteTempLabels();

        $labelPdf = $pdf->Output('S', 'PostNL Shipping Labels.pdf');

        return $labelPdf;
    }

    /**
     * FPDI doesn't use namespaces, and therefore may not be loaded properly.
     * This function can be used as fallback since Zend_Pdf is always included with Magento 2.
     *
     * @param $labels
     *
     * @return string
     * @throws \Zend_Pdf_Exception
     */
    private function getZendPdf($labels)
    {
        /** @var \Zend_Pdf $combinedLabels */
        $combinedLabels = $this->labelGenerator->combineLabelsPdf($labels);
        $renderedLabels = $combinedLabels->render();

        return $renderedLabels;
    }

    /**
     * FPDI expects the labels to be provided as files, therefore temporarily save each label in the var folder.
     *
     * @param $label
     *
     * @return string
     */
    private function saveTempLabel($label)
    {
        $tempFilePath = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . self::TEMP_LABEL_FOLDER;
        $tempFileName = md5(microtime()) . '-' . time() . '-' . self::TEMP_LABEL_FILENAME;
        $tempFile = $tempFilePath . DIRECTORY_SEPARATOR . $tempFileName;

        $this->ioFile->checkAndCreateFolder($tempFilePath);
        $this->ioFile->write($tempFile, $label);

        $this->tempFilesSaved[] = $tempFile;

        return $tempFile;
    }

    /**
     * Delete temporary labels from the var folder
     */
    private function deleteTempLabels()
    {
        array_walk(
            $this->tempFilesSaved,
            function ($tempfile, $key) {
                if ($this->ioFile->fileExists($tempfile)) {
                    $this->ioFile->rm($tempfile);
                }
            }
        );
    }
}
