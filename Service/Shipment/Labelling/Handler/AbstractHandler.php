<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Handler;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

abstract class AbstractHandler
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
     * @param $labelItem
     *
     * @return string
     */
    public function getTempLabel($labelItem)
    {
        /**
         * Decode the label received from PostNL.
         */
        // @codingStandardsIgnoreLine
        return $this->file->save($labelItem);
    }

    /**
     * Delete the file generated in the previous step.
     */
    public function cleanup()
    {
        $this->file->cleanup();
    }
}
