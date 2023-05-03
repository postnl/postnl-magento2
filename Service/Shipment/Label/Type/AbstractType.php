<?php

namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\File;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;

abstract class AbstractType
{
    /**
     * @var Fpdi
     */
    // @codingStandardsIgnoreLine
    protected $pdf;

    /**
     * @var FpdiFactory
     */
    // @codingStandardsIgnoreLine
    protected $fpdi;

    /**
     * @var File
     */
    private $file;

    /**
     * @param FpdiFactory $Fpdi
     * @param File        $file
     */
    public function __construct(
        FpdiFactory $Fpdi,
        File $file
    ) {
        $this->file = $file;
        $this->fpdi = $Fpdi;
    }

    /**
     * Fpdi expects the labels to be provided as files, therefore temporarily save each label in the var folder.
     *
     * @param ShipmentLabelInterface $label
     *
     * @return string
     */
    public function saveTempLabel(ShipmentLabelInterface $label)
    {
        /**
         * Decode the label received from PostNL.
         */
        // @codingStandardsIgnoreLine
        return $this->file->save(base64_decode($label->getLabel()));
    }

    /**
     * Delete the file generated in the previous step.
     */
    public function cleanup()
    {
        $this->file->cleanup();
    }

    /**
     * This function prevents that the $fpdi->create() method is called multiple times.
     */
    public function createPdf()
    {
        if ($this->pdf) {
            return;
        }

        $this->pdf = $this->fpdi->create();
    }
}
