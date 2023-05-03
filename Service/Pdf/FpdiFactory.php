<?php

namespace TIG\PostNL\Service\Pdf;

use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\Module\Manager;
use TIG\PostNL\Service\Shipment\Label\File;

/**
 * As Magento does auto generate the Fpdi class when using FpdiFactory we are doing this ourself.
 */
class FpdiFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var File
     */
    private $file;

    /**
     * @param ObjectManager $objectManager
     * @param Manager       $moduleManager
     * @param File          $file
     */
    public function __construct(
        ObjectManager $objectManager,
        Manager $moduleManager,
        File $file
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        if (!$this->moduleManager->isEnabled('Fooman_PrintOrderPdf')) {
            // @codingStandardsIgnoreLine
            return $this->objectManager->create(Fpdi::class, [
                'file' => $this->file,
                'orientation' => 'P',
                'unit' => 'mm',
                'size' => 'A4',
            ]);
        }

        return $this->constructTCPDF();
    }

    /**
     * @return mixed
     */
    private function constructTCPDF()
    {
        // @codingStandardsIgnoreLine
        return $this->objectManager->create(Fpdi::class, [
            'file' => $this->file,
            'orientation' => 'P',
            'unit' => 'mm',
            'size' => 'A4',
            'unicode' => true,
            'encoding' => 'UTF-8',
            'diskcache' => false,
            'pdfa' => false,
        ]);
    }

    /**
     * @param $pdf
     *
     * @return string
     * @throws \Exception
     */
    public function saveFile($pdf)
    {
        $file = $this->file->save($pdf);

        return $file;
    }

    public function cleanupFiles()
    {
        $this->file->cleanup();
    }
}
