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
namespace TIG\PostNL\Helper\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use TIG\PostNL\Model\ShipmentLabel;

class Generate
{
    const TEMP_LABEL_FOLDER = 'PostNL' . DIRECTORY_SEPARATOR . 'templabel';
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
     * @var \FPDI
     */
    private $Fpdf;

    /**
     * @param File           $ioFile
     * @param DirectoryList  $directoryList
     * @param LabelGenerator $labelGenerator
     * @param Fpdf          $Fpdf
     */
    public function __construct(
        File $ioFile,
        DirectoryList $directoryList,
        LabelGenerator $labelGenerator,
        Fpdf $Fpdf
    ) {
        $this->ioFile = $ioFile;
        $this->directoryList = $directoryList;
        $this->labelGenerator = $labelGenerator;
        $this->Fpdf = $Fpdf;
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

        $this->Fpdf->SetTitle('PostNL Shipping Labels');
        $this->Fpdf->SetAuthor('PostNL');
        $this->Fpdf->SetCreator('PostNL');

        $this->addLabelsToPdf($labels);

        $labelPdf = $this->Fpdf->Output('S', 'PostNL Shipping Labels.pdf');

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
     * @param ShipmentLabel[] $labels
     */
    private function addLabelsToPdf($labels)
    {
        foreach ($labels as $label) {
            // @codingStandardsIgnoreLine
            $tempLabelFile = $this->saveTempLabel(base64_decode($label->getLabel()));

            $this->Fpdf->addLabel($tempLabelFile, $label->getType());

            // @codingStandardsIgnoreLine
            $this->ioFile->rm($tempLabelFile);
        }
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
