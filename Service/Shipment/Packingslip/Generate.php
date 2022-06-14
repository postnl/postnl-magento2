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
namespace TIG\PostNL\Service\Shipment\Packingslip;

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReaderException;
use TIG\PostNL\Logging\Log;

class Generate
{
    /**
     * @var Log
     */
    private $logger;

    /**
     * @param Log $logger
     */
    public function __construct(Log $logger){
        $this->logger = $logger;
    }

    /**
     * @param array $labels
     *
     * @return string
     */
    public function run(array $labels)
    {
        $pdf = new Fpdi();

        foreach ($labels as $label) {
            $pdf = $this->addLabelToPdf($label, $pdf);
        }

        return $pdf->Output('S');
    }

    /**
     * @param string    $label
     * @param Fpdi $pdf
     *
     * @return Fpdi
     */
    private function addLabelToPdf($label, $pdf)
    {
        if(empty($label)) {
            return $pdf;
        }

        try {
            $stream = StreamReader::createByString($label);
            $pageCount = $pdf->setSourceFile($stream);
        } catch(PdfParserException $parserException) {
            $this->logger->error('[Service\Shipment\PackingSlip\Generate] Error while parsing sourcefile: ' . $parserException->getMessage());
            return $pdf;
        }

        for($pageIndex = 0; $pageIndex < $pageCount; $pageIndex++) {
            try {
                $templateId = $pdf->importPage($pageIndex + 1);
                $pageSize   = $pdf->getTemplateSize($templateId);

                if ($pageSize['width'] > $pageSize['height']) {
                    $pdf->AddPage('L', $pageSize);
                } else {
                    $pdf->AddPage('P', $pageSize);
                }

                $pdf->useTemplate($templateId);
            } catch (PdfParserException $fpdiException) {
                $this->logger->error('[Service\Shipment\PackingSlip\Generate] PdfParserException: ' . $fpdiException->getMessage());
            } catch (PdfReaderException $readerException) {
                $this->logger->error('[Service\Shipment\PackingSlip\Generate] ReaderException: ' . $readerException->getMessage());
            }
        }

        return $pdf;
    }
}
