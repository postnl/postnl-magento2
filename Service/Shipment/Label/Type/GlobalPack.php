<?php

namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;

class GlobalPack extends EPS
{
    /**
     * The labels of these Priority GP countries are not supposed to be rotated.
     *
     * @var array
     */
    private $excludedCountries = ["US"];

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \FPDF|Fpdi
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function process(ShipmentLabelInterface $label)
    {
        $productCode = $label->getProductCode();
        $shipment    = $label->getShipment();
        $filename    = $this->saveTempLabel($label);

        $this->createPdf();
        $count = $this->pdf->setSourceFile($filename);

        for ($pageNo = 1; $pageNo <= $count; $pageNo++) {
            $this->processLabels($shipment, $pageNo, $productCode);

            // Reset templateInserted so that subsequent pages will be included
            $this->setTemplateInserted(false);
        }

        return $this->pdf;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $page
     * @param                   $code
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function processLabels(ShipmentInterface $shipment, $page, $code)
    {
        if (!$this->isRotatedProduct($code)
            && $this->isPriorityProduct($code)
        ) {
            $countryId = $shipment->getShipmentCountry();
            $this->insertRotated($page, $countryId);
        }

        if (!$this->getTemplateInserted()) {
            $this->insertRegular($page);
        }
    }

    /**
     * @param $country string
     *
     * @return bool
     */
    public function isExcludedCountry($country)
    {
        return in_array($country, $this->excludedCountries);
    }

    /**
     * Since Priority GP has its own resolution and size, we override this
     * method.
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function insertRotated($page, $countryId)
    {
        $this->setTemplateInserted(true);
        $this->pdf->AddPage('P', Fpdi::PAGE_SIZE_A6);

        $pageId = $this->pdf->importPage($page);

        if (!$this->isExcludedCountry($countryId)) {
            $this->pdf->Rotate(90);
            $this->pdf->useTemplate($pageId, -130, 0, 150, 210);
            $this->pdf->Rotate(0);

            return;
        }

        $this->pdf->useTemplate($pageId, 0, 0, 103, 150);
    }

    /**
     * This method is used for regular GlobalPack labels.
     *
     * @param $page
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function insertRegular($page)
    {
        $this->setTemplateInserted(true);

        $templateId   = $this->pdf->importPage($page);
        $templateSize = $this->pdf->getTemplateSize($templateId);
        $orientation  = $templateSize['width'] > $templateSize['height'] ? 'L' : 'P';

        $this->pdf->AddPage($orientation, [$templateSize['width'], $templateSize['height']]);
        $this->pdf->useTemplate($templateId, 0, 0, $templateSize['width'], $templateSize['height']);
    }
}
