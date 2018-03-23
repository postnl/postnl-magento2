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

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;

class MergeWithLabels
{
    /**
     * @var int
     */
    private $packingslipYPos;

    /**
     * @var GetLabels
     */
    private $getLabels;

    /**
     * @var LabelGenerate
     */
    private $labelGenerator;

    /**
     * @var Generate
     */
    private $packingslipGenerator;

    /**
     * @var FpdiFactory
     */
    private $fpdiFactory;

    /**
     * @var File
     */
    private $file;

    /**
     * @param GetLabels                  $getLabels
     * @param LabelGenerate              $labelGenerator
     * @param Generate                   $packingslipGenerator
     * @param FpdiFactory                $fpdiFactory
     * @param File                       $file
     */
    public function __construct(
        GetLabels $getLabels,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator,
        FpdiFactory $fpdiFactory,
        File $file
    ) {
        $this->getLabels = $getLabels;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
        $this->fpdiFactory = $fpdiFactory;
        $this->file = $file;
    }

    /**
     * @param int $packingslipYPos
     */
    public function setY($packingslipYPos)
    {
        $this->packingslipYPos = $packingslipYPos;
    }

    /**
     * @param int    $shipmentId
     * @param string $packingslip
     *
     * @return string
     */
    public function mergeTogether($shipmentId, $packingslip)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return $packingslip;
        }

        if ($this->packingslipYPos > 400) {
            $firstLabel = array_shift($labels);
            // @codingStandardsIgnoreLine
            $label = base64_decode($firstLabel->getLabel());
            $packingslip = $this->mergeFirstLabel($label, $packingslip);
        }

        if (empty($labels)) {
            return $packingslip;
        }

        $packingslipPdf = $this->addLabelsToPackingslip($packingslip, $labels);
        return $packingslipPdf;
    }

    /**
     * @param int    $shipmentId
     * @param string $packingslip
     *
     * @return string
     */
    public function mergeSeparate($shipmentId, $packingslip)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return $packingslip;
        }

        $packingslipPdf = $this->addLabelsToPackingslip($packingslip, $labels);

        return $packingslipPdf;
    }

    /**
     * @param string $packingslip
     * @param array $labels
     *
     * @return string
     */
    private function addLabelsToPackingslip($packingslip, $labels)
    {
        $labelPdf = $this->labelGenerator->run($labels, true);
        $packingslipPdf = $this->packingslipGenerator->run([$packingslip, $labelPdf]);

        return $packingslipPdf;
    }

    /**
     * @param string    $label
     * @param string $packingslip
     *
     * @return string
     */
    private function mergeFirstLabel($label, $packingslip)
    {
        /** @var Fpdi $pdf */
        $pdf = $this->fpdiFactory->create();

        $packingslipFile = $this->file->save($packingslip);
        $pdf->addMultiplePages($packingslipFile, 0, 0);

        $labelFile = $this->file->save($label);
        $pdf->Rotate(90);
        $pdf->addSinglePage(
            $labelFile,
            $pdf->pixelsToPoints(-1037),
            $pdf->pixelsToPoints(413),
            $pdf->pixelsToPoints(538)
        );
        $pdf->Rotate(0);

        $this->file->cleanup();

        return $pdf->Output('s');
    }
}
