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

use TIG\PostNL\Config\Provider\Webshop;

// @codingStandardsIgnoreFile
// @todo: Needs refactoring in 2 seprate classes: 1 for A4 labels and 1 for A6 labels.
class Fpdf extends \FPDI
{
    const MAX_LABELS_PER_PAGE = 4;

    const PAGE_SIZE_A6 = [105, 148];

    /** @var int $labelCounter */
    private $labelCounter;

    /**
     * @var Positions
     */
    private $positions;

    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @param Positions $positions
     * @param Webshop   $webshop
     * @param string    $orientation
     * @param string    $unit
     * @param string    $size
     */
    public function __construct(
        Positions $positions,
        Webshop $webshop,
        $orientation = 'P',
        $unit = 'mm',
        $size = 'A4'
    ) {
        $this->positions = $positions;
        $this->webshop = $webshop;
        $this->setLabelCounter(self::MAX_LABELS_PER_PAGE);

        parent::__construct($orientation, $unit, $size);
    }

    /**
     * @param string $labelFileName
     * @param string $labelType
     * @param        $shipmentType
     */
    public function addLabel($labelFileName, $labelType, $shipmentType)
    {
        $this->updatePage($shipmentType);
        $this->setSourceFile($labelFileName);
        $this->mergePage($labelType, $shipmentType);
    }

    /**
     * Create a new page when necessary
     *
     * @param $shipmentType
     */
    public function updatePage($shipmentType)
    {
        $this->increaseLabelCounter();

        $labelSize = $this->webshop->getLabelSize();

        if ($labelSize == 'A6') {
            $this->addA6Page($shipmentType);
        }

        if ($this->getLabelCounter() > self::MAX_LABELS_PER_PAGE) {
            $this->resetLabelCounter();
            $this->AddPage('L', 'A4');
        }
    }

    /**
     * @return int
     */
    public function getLabelCounter()
    {
        return $this->labelCounter;
    }

    /**
     * @param int $labelCounter
     *
     * @return $this
     */
    public function setLabelCounter($labelCounter)
    {
        $this->labelCounter = $labelCounter;
    }

    public function increaseLabelCounter()
    {
        $this->labelCounter++;
    }

    /**
     *
     */
    public function resetLabelCounter()
    {
        $this->labelCounter = 1;
    }

    /**
     * @param $shipmentType
     */
    private function addA6Page($shipmentType)
    {
        $this->setLabelCounter(3);

        if ($shipmentType == 'EPS') {
            $this->AddPage('P', self::PAGE_SIZE_A6);
            return;
        }

        $this->AddPage('L', self::PAGE_SIZE_A6);
    }

    /**
     * @param $labelType
     * @param $shipmentType
     */
    private function mergePage($labelType, $shipmentType)
    {
        $labelSize = $this->webshop->getLabelSize();
        $templateIndex = $this->importPage(1);

        if ($labelSize == 'A6') {
            $this->mergePageA6($templateIndex, $labelType, $shipmentType);
        }

        if ($labelSize == 'A4') {
            $this->mergePageA4($templateIndex, $labelType);
        }
    }

    /**
     * @param $templateIndex
     */
    private function mergePageA6($templateIndex, $labelType, $shipmentType)
    {
        if ($shipmentType == 'EPS') {
            $sizes  = static::PAGE_SIZE_A6;
            $width  = $sizes[0];
            $height = $sizes[1];
            $this->useTemplate($templateIndex, 1, 0, $width, $height);
            return;
        }

        $pdfPageWidth = $this->GetPageWidth();
        $pdfPageHeight = $this->GetPageHeight();
        $position = $this->positions->getForPosition(
            $pdfPageWidth,
            $pdfPageHeight,
            $this->getLabelCounter(),
            $labelType
        );

        $this->useTemplate($templateIndex, $position['x'], $position['y'], $position['w']);
    }

    /**
     * @param $templateIndex
     * @param $labelType
     */
    private function mergePageA4($templateIndex, $labelType)
    {
        $pdfPageWidth = $this->GetPageWidth();
        $pdfPageHeight = $this->GetPageHeight();
        $position = $this->positions->getForPosition(
            $pdfPageWidth,
            $pdfPageHeight,
            $this->getLabelCounter(),
            $labelType
        );

        $this->useTemplate($templateIndex, $position['x'], $position['y'], $position['w']);
    }
}
