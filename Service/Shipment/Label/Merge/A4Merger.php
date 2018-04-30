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
namespace TIG\PostNL\Service\Shipment\Label\Merge;

use TIG\PostNL\Service\Pdf\Fpdi;

class A4Merger extends AbstractMerger implements MergeInterface
{
    /**
     * @var int
     */
    private $labelCounter = 0;

    /**
     * @param Fpdi[] $labels
     * @codingStandardsIgnoreStart
     * @param bool  $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                            This parameter indicates whether to reuse the existing label PDF
     *                            @TODO Refactor to a cleaner way rather than chaining all the way to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     * @codingStandardsIgnoreEnd
     *
     * @return Fpdi
     */
    public function files(array $labels, $createNewPdf = false)
    {
        //By resetting the counter, labels will start in the upper-left when creating a new PDF
        if ($createNewPdf) {
            $this->labelCounter = 0;
        }

        $this->pdf = $this->createPdf(false, $createNewPdf);
        foreach ($labels as $label) {
            $this->addPagesToPdf($label);
        }

        $this->file->cleanup();

        return $this->pdf;
    }

    /**
     * @param Fpdi $label
     */
    private function addPagesToPdf($label)
    {
        // @codingStandardsIgnoreLine
        $filename = $this->file->save($label->Output('S'));
        $count    = $this->pdf->setSourceFile($filename);

        for ($pageNo = 1; $pageNo <= $count; $pageNo++) {
            $templateId   = $this->pdf->importPage($pageNo);
            $templateSize = $this->pdf->getTemplateSize($templateId);

            $this->addPageToPdf($templateId, $templateSize, $count);
        }
    }

    /**
     * Add page to the pdf with correct orientation.
     *
     * @param $templateId
     * @param $templateSize
     * @param $count
     */
    private function addPageToPdf($templateId, $templateSize, $count)
    {
        $orientation = $templateSize['w'] > $templateSize['h'] ? 'L' :'P';

        if ($this->shouldAddNewPage($orientation)) {
            $this->labelCounter = 0;
            $this->pdf->AddPage($orientation, 'A4');
        }

        if ($count <= 1 && $orientation == 'L') {
            $this->increaseCounter();
        }

        if ($this->pdf->PageNo() == 0 || $orientation == 'P') {
            $this->labelCounter = 1;
            $this->pdf->AddPage($orientation, 'A4');
        }

        list($xPosition, $yPosition) = $this->getPosition();
        $this->setLastOrientation($orientation);
        $this->pdf->useTemplate($templateId, $xPosition, $yPosition);
    }

    /**
     * Adds an new page if the counter is too high.
     */
    private function increaseCounter()
    {
        $this->labelCounter++;

        if ($this->labelCounter > 4) {
            $this->labelCounter = 1;
            $this->pdf->addPage('L', 'A4');
        }
    }

    /**
     * Get the position for the label based on the counter.
     *
     * @return array
     */
    private function getPosition()
    {
        if ($this->labelCounter == 2) {
            return [0, Fpdi::PAGE_SIZE_A6_WIDTH];
        }

        if ($this->labelCounter == 3) {
            return [0, 0];
        }

        if ($this->labelCounter == 4) {
            return [Fpdi::PAGE_SIZE_A6_HEIGHT, Fpdi::PAGE_SIZE_A6_WIDTH];
        }

        return [Fpdi::PAGE_SIZE_A6_HEIGHT, 0];
    }
}
