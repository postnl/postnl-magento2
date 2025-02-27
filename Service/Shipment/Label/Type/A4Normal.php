<?php
namespace TIG\PostNL\Service\Shipment\Label\Type;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;

class A4Normal extends AbstractType implements TypeInterface
{
    private bool $templateInserted = false;

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return Fpdi
     *
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    public function process(ShipmentLabelInterface $label): Fpdi
    {
        $filename = $this->saveTempLabel($label);

        $this->createPdf();
        $this->pdf->AddPage('P');
        $this->pdf->setSourceFile($filename);

        $this->insertRegular();

        return $this->pdf;
    }

    /**
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    private function insertRegular(): void
    {
        $this->setTemplateInserted(true);
        $pageId = $this->pdf->importPage(1);
        $this->pdf->Rotate(0);
        $this->pdf->useTemplate($pageId, 0, 0);
        $this->pdf->Rotate(0);
    }

    private function setTemplateInserted(bool $value): void
    {
        $this->templateInserted = $value;
    }

    private function getTemplateInserted(): bool
    {
        return $this->templateInserted;
    }
}
