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
namespace TIG\PostNL\Service\Shipment\Label\Type;

use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;

class Domestic extends AbstractType implements TypeInterface
{
    /**
     * These are return labels that should be rotated, separate from their normal shipping labels
     */
    private $returnProducts = [4946];

    /** @var bool */
    private $templateInserted = false;

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
    public function process(ShipmentLabelInterface $label)
    {
        $filename = $this->saveTempLabel($label);

        $this->createPdf();
        $this->pdf->AddPage('P', Fpdi::PAGE_SIZE_A6);
        $this->pdf->setSourceFile($filename);

        if ($this->rotateReturnProduct($label)) {
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
     */
    private function rotateReturnProduct($label)
    {
        return (in_array($label->getProductCode(), $this->returnProducts) && $label->getReturnLabel());
    }

    /**
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    private function insertRotated()
    {
        $this->setTemplateInserted(true);
        $pageId = $this->pdf->importPage(1);
        $this->pdf->useTemplate($pageId, 0, 0, Fpdi::PAGE_SIZE_A6_WIDTH, Fpdi::PAGE_SIZE_A6_HEIGHT);
    }

    /**
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    private function insertRegular()
    {
        $this->setTemplateInserted(true);
        $pageId = $this->pdf->importPage(1);
        $this->pdf->Rotate(90);
        $this->pdf->useTemplate($pageId, - 130, 0);
        $this->pdf->Rotate(0);
    }

    /**
     * @param $value
     */
    private function setTemplateInserted($value)
    {
        $this->templateInserted = $value;
    }

    /**
     * @return bool
     */
    private function getTemplateInserted()
    {
        return $this->templateInserted;
    }
}
