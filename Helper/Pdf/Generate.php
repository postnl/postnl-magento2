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
use TIG\PostNL\Model\ShipmentLabel;

/**
 * Class Generate
 *
 * @package TIG\PostNL\Helper\Pdf
 */
class Generate
{
    const MAX_LABELS_PER_PAGE = 4;

    const TEMP_LABEL_FOLDER = 'log' . DIRECTORY_SEPARATOR . 'PostNL' . DIRECTORY_SEPARATOR . 'templabel';
    const TEMP_LABEL_FILENAME = 'TIG_PostNL_temp.pdf';

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var Positions
     */
    private $positions;

    /**
     * @var \FPDI
     */
    private $FPDI;

    /**
     * @param File           $ioFile
     * @param DirectoryList  $directoryList
     * @param LabelGenerator $labelGenerator
     * @param Positions      $positions
     * @param \FPDI          $FPDI
     */
    public function __construct(
        File $ioFile,
        DirectoryList $directoryList,
        LabelGenerator $labelGenerator,
        Positions $positions,
        \FPDI $FPDI
    ) {
        $this->ioFile = $ioFile;
        $this->directoryList = $directoryList;
        $this->labelGenerator = $labelGenerator;
        $this->positions = $positions;
        $this->FPDI = $FPDI;
    }

    /**
     * @param ShipmentLabel[]|ShipmentLabel $labels
     *
     * @return string
     */
    public function get($labels)
    {
        if (!is_array($labels)) {
            /** @var ShipmentLabel[] $labels */
            $labels = [$labels];
        }

        if (!class_exists('FPDI')) {
            return $this->getZendPdf($labels);
        }

        $this->FPDI->SetTitle('PostNL Shipping Labels');
        $this->FPDI->SetAuthor('PostNL');
        $this->FPDI->SetCreator('PostNL');

        $this->addLabelsToPdf($labels);

        $labelPdf = $this->FPDI->Output('S', 'PostNL Shipping Labels.pdf');

        return $labelPdf;
    }

    /**
     * FPDI doesn't use namespaces, and therefore may not be loaded properly.
     * This function can be used as fallback since Zend_Pdf is always included with Magento 2.
     *
     * @param ShipmentLabel[] $labels
     *
     * @return string
     * @throws \Zend_Pdf_Exception
     */
    private function getZendPdf($labels)
    {
        $labelData = [];

        foreach ($labels as $label) {
            // @codingStandardsIgnoreLine
            $labelData[] = base64_decode($label->getLabel());
        }

        /** @var \Zend_Pdf $combinedLabels */
        $combinedLabels = $this->labelGenerator->combineLabelsPdf($labelData);
        $renderedLabels = $combinedLabels->render();

        return $renderedLabels;
    }

    /**
     * @param $labels
     */
    private function addLabelsToPdf($labels)
    {
        $labelCounter = $this->managePdfPage();

        foreach ($labels as $label) {
            // @codingStandardsIgnoreLine
            $tempLabelFile = $this->saveTempLabel(base64_decode($label->getLabel()));

            $pdfPageWidth = $this->FPDI->GetPageWidth();
            $pdfPageHeight = $this->FPDI->GetPageHeight();
            $position = $this->positions->get($pdfPageWidth, $pdfPageHeight, $label->getType(), $labelCounter);

            $this->FPDI->setSourceFile($tempLabelFile);
            $templateIndex = $this->FPDI->importPage(1);
            $this->FPDI->useTemplate($templateIndex, $position['x'], $position['y'], $position['w']);

            // @codingStandardsIgnoreLine
            $this->ioFile->rm($tempLabelFile);

            $labelCounter = $this->managePdfPage($labelCounter);
        }
    }

    /**
     * @param int $labelCount
     *
     * @return int
     */
    private function managePdfPage($labelCount = self::MAX_LABELS_PER_PAGE)
    {
        $labelCount++;

        if ($labelCount > self::MAX_LABELS_PER_PAGE) {
            $labelCount = 1;
            $this->FPDI->AddPage('L', 'A4');
        }

        return $labelCount;
    }

    /**
     * FPDI expects the labels to be provided as files, therefore temporarily save each label in the var folder.
     *
     * @param string $label
     *
     * @return string
     */
    private function saveTempLabel($label)
    {
        $tempFilePath = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . self::TEMP_LABEL_FOLDER;
        $tempFileName = sha1(microtime()) . '-' . time() . '-' . self::TEMP_LABEL_FILENAME;
        $tempFile = $tempFilePath . DIRECTORY_SEPARATOR . $tempFileName;

        $this->ioFile->checkAndCreateFolder($tempFilePath);
        $this->ioFile->write($tempFile, $label);

        return $tempFile;
    }
}
