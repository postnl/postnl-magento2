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

use Magento\Framework\Message\Manager as MessageManager;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;

// @codingStandardsIgnoreFile
class MergeWithLabels
{
    // @codingStandardsIgnoreLine
    private $rotation  = 90;
    // @codingStandardsIgnoreLine
    private $xPosition = -1037;
    // @codingStandardsIgnoreLine
    private $yPosition = 413;
    // @codingStandardsIgnoreLine
    private $width     = 538;

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
     * @var MessageManager $messageManager
     */
    private $messageManager;

    /**
     * @param GetLabels                  $getLabels
     * @param LabelGenerate              $labelGenerator
     * @param Generate                   $packingslipGenerator
     * @param FpdiFactory                $fpdiFactory
     * @param MessageManager             $messageManager
     */
    public function __construct(
        GetLabels $getLabels,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator,
        FpdiFactory $fpdiFactory,
        MessageManager $messageManager
    ) {
        $this->getLabels = $getLabels;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
        $this->fpdiFactory = $fpdiFactory;
        $this->messageManager = $messageManager;
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
     * @param bool   $mergeFirstLabel
     * @param bool   $confirm
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    public function merge($shipmentId, $packingslip, $mergeFirstLabel = false, $confirm = true)
    {
        $labels = $this->getLabels->get($shipmentId, $confirm);
        if (empty($labels)) {
            return $packingslip;
        }

        if (isset($labels['errors'])) {
            return $labels['errors'];
        }

        if (isset($labels['notices'])) {
            array_walk($labels['notices'], function ($notice) {
                $this->messageManager->addNoticeMessage($notice);
            });
            unset($labels['notices']);
        }

        if ($mergeFirstLabel && $this->canMergeFirstLabel($labels[0])) {
            $firstLabel = array_shift($labels);
            $label = base64_decode($firstLabel->getLabel());
            $packingslip = $this->mergeFirstLabel($label, $packingslip, $firstLabel->getType());
        }

        if (empty($labels)) {
            return $packingslip;
        }

        $packingslipPdf = $this->addLabelsToPackingslip($packingslip, $labels);
        return $packingslipPdf;
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param ShipmentLabelInterface $firstLabel
     *
     * @return bool
     */
    private function canMergeFirstLabel($firstLabel)
    {
        $labelTypeGP = strtolower(ProductInfo::SHIPMENT_TYPE_GP);
        if ($this->packingslipYPos <= 400 || $firstLabel->getType() == $labelTypeGP) {
            return false;
        }

        return true;
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
     * @param string $label
     * @param string $packingslip
     * @param string $type
     *
     * @return string
     */
    private function mergeFirstLabel($label, $packingslip, $type = null)
    {
        /** @var Fpdi $pdf */
        $pdf = $this->fpdiFactory->create();
        $packingslipFile = $this->fpdiFactory->saveFile($packingslip);
        $pdf->addMultiplePages($packingslipFile, 0, 0);

        $labelFile = $this->fpdiFactory->saveFile($label);
        $pdf = $this->addLabelToPdf($labelFile, $pdf, $type);
        $this->fpdiFactory->cleanupFiles();

        return $pdf->Output('s');
    }

    /**
     * @param      $labelFile
     * @param Fpdi $pdf
     * @param      $type
     *
     * @return Fpdi
     */
    private function addLabelToPdf($labelFile, Fpdi $pdf, $type)
    {
        if ($type == strtolower(ProductInfo::SHIPMENT_TYPE_EPS)) {
            $this->setEpsPosition();
        }

        $pdf->Rotate($this->rotation);
        $pdf->addSinglePage(
            $labelFile,
            $pdf->pixelsToPoints($this->xPosition),
            $pdf->pixelsToPoints($this->yPosition),
            $pdf->pixelsToPoints($this->width)
        );

        $pdf->Rotate(0);

        //Always reset back to the default position;
        $this->resetPosition();

        return $pdf;
    }

    private function setEpsPosition()
    {
        $this->rotation  = 0;
        $this->xPosition = 400;
        $this->yPosition = 560;
        $this->width     = 390;
    }

    private function resetPosition()
    {
        $this->rotation  = 90;
        $this->xPosition = -1037;
        $this->yPosition = 413;
        $this->width     = 538;
    }
}
