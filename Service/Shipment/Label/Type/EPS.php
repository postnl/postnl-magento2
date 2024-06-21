<?php

namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;

class EPS extends Domestic
{
    /**
     * These are combiLabel products, these codes are returned by PostNL in the label response (ProductCodeDelivery)
     */
    private $rotated = [4940, 4950, 4983, 4985, 4986, 3622, 3642, 3659];

    /**
     * The product codes, returned by the label response, to alter label generation for priority products.
     *
     * @var array
     */
    private $priority = [6350, 6550, 6940, 6942];

    /**
     * @var bool
     */
    private $templateInserted = false;

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \FPDF
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function process(ShipmentLabelInterface $label)
    {
        $filename = $this->saveTempLabel($label);

        $this->createPdf();
        $this->pdf->AddPage('P', Fpdi::PAGE_SIZE_A6);
        $this->pdf->setSourceFile($filename);

        if ($this->shouldRotate($label)) {
            $this->insertRotated();
        }

        if (!$this->getTemplateInserted()) {
            $this->insertRegular();
        }

        return $this->pdf;
    }

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return bool
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function shouldRotate($label)
    {
        $productCode = $label->getProductCode();

        if ($this->isRotated()) {
            return true;
        }

        if ($this->isRotatedProduct($productCode) && !$this->isPriorityProduct($productCode)) {
            return true;
        }

        return false;
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function isRotatedProduct($code)
    {
        return in_array($code, $this->rotated);
    }

    /**
     * This is a label with a vertical orientation, so rotate it before inserting.
     *
     * @param bool $importPage
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function insertRotated()
    {
        $this->setTemplateInserted(true);
        $pageId = $this->pdf->importPage(1);
        $this->pdf->Rotate(90);
        $this->pdf->useTemplate($pageId, - 130, 0);
        $this->pdf->Rotate(0);
    }

    /**
     * This is a default label, it does not need any modification.
     *
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function insertRegular()
    {
        $this->setTemplateInserted(true);
        $pageId = $this->pdf->importPage(1);
        $this->pdf->useTemplate($pageId, 0, 0, Fpdi::PAGE_SIZE_A6_WIDTH, Fpdi::PAGE_SIZE_A6_HEIGHT);
    }

    /**
     * @param $code
     *
     * @return bool
     */
    public function isPriorityProduct($code)
    {
        return in_array($code, $this->priority);
    }

    /**
     * @return bool
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function isRotated()
    {
        $pageId = $this->pdf->importPage(1);
        $sizes = $this->pdf->getTemplateSize($pageId);

        if (isset($sizes['width']) && isset($sizes['height']) && $sizes['width'] > $sizes['height']) {
            return true;
        }

        return false;
    }

    /**
     * @param $value
     */
    // @codingStandardsIgnoreLine
    public function setTemplateInserted($value)
    {
        $this->templateInserted = $value;
    }

    /**
     * @return bool
     */
    public function getTemplateInserted()
    {
        return $this->templateInserted;
    }
}
