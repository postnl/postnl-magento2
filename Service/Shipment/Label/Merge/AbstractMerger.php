<?php

namespace TIG\PostNL\Service\Shipment\Label\Merge;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

class AbstractMerger
{
    /**
     * @var Fpdi
     */
    // @codingStandardsIgnoreLine
    protected $pdf;

    /**
     * @var File
     */
    // @codingStandardsIgnoreLine
    protected $file;

    /**
     * @var FpdiFactory
     */
    // @codingStandardsIgnoreLine
    protected $fpdiFactory;

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $lastOrientation;

    /**
     * @param FpdiFactory $fpdiFactory
     * @param File $file
     */
    public function __construct(
        FpdiFactory $fpdiFactory,
        File $file
    ) {
        $this->fpdiFactory = $fpdiFactory;
        $this->file = $file;
    }

    /**
     * @param bool $addPage
     * @codingStandardsIgnoreStart
     * @param bool $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                           This parameter indicates whether to reuse the existing label PDF
     *                           @TODO Refactor to a cleaner way rather than chaining all the way to here
     * @@codingStandardsIgnoreEnd
     *
     * @return \FPDF|mixed|null|\PDF
     */
    // @codingStandardsIgnoreLine
    protected function createPdf($addPage = false, $createNewPdf = false)
    {
        static $pdf = null;

        if ($pdf && !$createNewPdf) {
            return $pdf;
        }

        $pdf = $this->fpdiFactory->create();

        if ($addPage) {
            $pdf->addPage('P', 'A4');
        }

        return $pdf;
    }

    /**
     * @param $orientation
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function shouldAddNewPage($orientation)
    {
        if (!($this->isOrientationDifferent($orientation) && $this->pdf->PageNo() !== 0)) {
            return false;
        }

        // Switching back from L to P will add a new page further in the process.
        if ($this->lastOrientation == 'L') {
            return false;
        }

        return true;
    }

    /**
     * @param $orientation
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function isOrientationDifferent($orientation)
    {
        return $this->lastOrientation !== $orientation;
    }

    /**
     * @param $orientation
     */
    // @codingStandardsIgnoreLine
    protected function setLastOrientation($orientation)
    {
        $this->lastOrientation = $orientation;
    }
}
