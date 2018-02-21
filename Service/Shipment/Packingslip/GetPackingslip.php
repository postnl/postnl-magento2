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

use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

class GetPackingslip
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var PdfShipment
     */
    private $pdfShipment;

    /**
     * @var GetLabels
     */
    private $getLabels;

    /**
     * @var LabelGenerate
     */
    private $labelGenerator;

    /**
     * @var PackingslipGenerate
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
     * GetPackingslip constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentLabelRepository
     * @param PdfShipment                 $pdfShipment
     * @param GetLabels                   $getLabels
     * @param LabelGenerate               $labelGenerator
     * @param Generate                    $packingslipGenerator
     * @param FpdiFactory                 $fpdiFactory
     * @param File                        $file
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentLabelRepository,
        PdfShipment $pdfShipment,
        GetLabels $getLabels,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator,
        FpdiFactory $fpdiFactory,
        File $file
    ) {
        $this->shipmentRepository = $shipmentLabelRepository;
        $this->pdfShipment = $pdfShipment;
        $this->getLabels = $getLabels;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
        $this->fpdiFactory = $fpdiFactory;
        $this->file = $file;
    }

    /**
     * @param int $shipmentId
     *
     * @return string
     */
    public function get($shipmentId)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        if (!$shipment) {
            return '';
        }

        $magentoShipment = $shipment->getShipment();
        $packingSlip = $this->pdfShipment->getPdf([$magentoShipment]);
        $currentYPosition = $this->pdfShipment->y;
//        $packingSlip = $packingSlip->render();

        $packingSlipPdf = $this->addLabels($shipmentId, $packingSlip, $currentYPosition);

        return $packingSlipPdf;
    }

    /**
     * @param int       $shipmentId
     * @param \Zend_Pdf $packingslip
     *
     * @param           $currentYPosition
     *
     * @return string
     */
    private function addLabels($shipmentId, $packingslip, $currentYPosition)
    {
        $labels = $this->getLabels->get($shipmentId);
        $pdfList = [];
        $mergedLabel = $packingslip->render();

        if ($currentYPosition > 400) {
            $mergedLabel = $this->mergeFirstLabel(array_shift($labels), $packingslip);
        }

        $pdfList[] = $mergedLabel;

        if (count($labels) > 0) {
            $labelPdf = $this->labelGenerator->run($labels, true);
            $pdfList[] = $labelPdf;
        }

        $packingslipPdf = $this->packingslipGenerator->run($pdfList);

        return $packingslipPdf;
    }

    /**
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface $label
     * @param \Zend_Pdf                                   $packingslip
     *
     * @return string
     */
    private function mergeFirstLabel($label, $packingslip)
    {
        /** @var Fpdi $pdf */
        $pdf = $this->fpdiFactory->create();

        foreach ($packingslip->pages as $page) {
            $packingslipPdfPage = new \Zend_Pdf();
            $packingslipPdfPage->pages[] = clone $page;

            $packingslipFile = $this->file->save($packingslipPdfPage->render());

            $pdf->AddPage('P', 'A4');
            $pdf->setSourceFile($packingslipFile);
            $pageId = $pdf->importPage(1);
            $pdf->useTemplate($pageId, 0, 0);
        }

        $labelFile = $this->file->save(base64_decode($label->getLabel()));
        $pdf->Rotate(90);
        $pdf->setSourceFile($labelFile);
        $pageId = $pdf->importPage(1);
        $pdf->useTemplate($pageId, $this->pix2pt(-1037), $this->pix2pt(413), $this->pix2pt(538));
        $pdf->Rotate(0);

        $this->file->cleanup();

        return $pdf->Output('s');
    }

    /**
     * Converts pixels to points. 3.8 pixels is 1 pt in pdfs.
     *
     * @param int $pixels
     *
     * @return int
     */
    private function pix2pt($pixels = 0)
    {
        $points = 0;

        if ($pixels != 0) {
            $points = round($pixels / 3.8, 1);
        }

        return $points;
    }
}
