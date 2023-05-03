<?php

namespace TIG\PostNL\Service\Shipment\Packingslip;

use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;
use Magento\Framework\Module\Manager;
use Zend_Pdf_Exception;

/**
 * Class Factory
 *
 * This is needed so we can check if Fooman PdfCustomiser is installed or not.
 */
class Factory
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var PdfShipment
     */
    private $magentoPdf;

    /**
     * @var int
     */
    private $yCoordinate = 0;

    /**
     * @var Compatibility\FoomanPdfCustomiser
     */
    private $foomanPdfCustomiser;

    /**
     * @var Compatibility\XtentoPdfCustomizer
     */
    private $xtentoPdfCustomizer;

    /**
     * @param Manager                           $manager
     * @param PdfShipment                       $pdfShipment
     * @param Compatibility\FoomanPdfCustomiser $foomanPdfCustomiser
     * @param Compatibility\XtentoPdfCustomizer $xtentoPdfCustomizer
     */
    public function __construct(
        Manager $manager,
        PdfShipment $pdfShipment,
        Compatibility\FoomanPdfCustomiser $foomanPdfCustomiser,
        Compatibility\XtentoPdfCustomizer $xtentoPdfCustomizer
    ) {
        $this->moduleManager   = $manager;
        $this->magentoPdf      = $pdfShipment;
        $this->foomanPdfCustomiser = $foomanPdfCustomiser;
        $this->xtentoPdfCustomizer = $xtentoPdfCustomizer;
    }

    /**
     * @param      $magentoShipment
     * @param bool $forceMagento
     *
     * @return string
     * @throws NotFoundException
     * @throws Zend_Pdf_Exception
     */
    public function create($magentoShipment, $forceMagento = false)
    {
        if (!$forceMagento && $this->moduleManager->isEnabled('Fooman_PdfCustomiser')) {
            return $this->foomanPdfCustomiser->getPdf($this, $magentoShipment);
        }

        if (!$forceMagento &&
            $this->moduleManager->isEnabled('Xtento_PdfCustomizer') &&
            $this->xtentoPdfCustomizer->isShipmentPdfEnabled()
        ) {
            return $this->xtentoPdfCustomizer->getPdf($magentoShipment);
        }

        $renderer = $this->magentoPdf->getPdf([$magentoShipment]);
        // @codingStandardsIgnoreLine
        $this->changeY($this->magentoPdf->y);
        return $renderer->render();
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->yCoordinate;
    }

    /**
     * @param $coordinate
     */
    public function changeY($coordinate)
    {
        $this->yCoordinate = $coordinate;
    }
}
