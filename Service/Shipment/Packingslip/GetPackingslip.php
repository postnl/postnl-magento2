<?php

namespace TIG\PostNL\Service\Shipment\Packingslip;

use TIG\PostNL\Service\Shipment\Packingslip\Factory as PdfFactory;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ShowShippingLabel;
use TIG\PostNL\Service\Shipment\Packingslip\Items\Barcode;

class GetPackingslip
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var PdfFactory
     */
    private $pdfShipment;

    /**
     * @var LabelAndPackingslipOptions
     */
    private $labelAndPackingslipOptions;

    /**
     * @var MergeWithLabels
     */
    private $mergeWithLabels;

    /**
     * @var Barcode
     */
    private $barcodeMerger;

    /**
     * GetPackingslip constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentLabelRepository
     * @param PdfFactory                 $pdfShipment
     * @param LabelAndPackingslipOptions  $labelAndPackingslipOptions
     * @param MergeWithLabels             $mergeWithLabels
     * @param Barcode                     $barcode
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentLabelRepository,
        PdfFactory $pdfShipment,
        LabelAndPackingslipOptions $labelAndPackingslipOptions,
        MergeWithLabels $mergeWithLabels,
        Barcode $barcode
    ) {
        $this->shipmentRepository = $shipmentLabelRepository;
        $this->pdfShipment = $pdfShipment;
        $this->labelAndPackingslipOptions = $labelAndPackingslipOptions;
        $this->mergeWithLabels = $mergeWithLabels;
        $this->barcodeMerger = $barcode;
    }

    /**
     * @param int $shipmentId
     * @param bool $withLabels
     * @param bool $confirm
     *
     * @return string|array
     */
    public function get($shipmentId, $withLabels = true, $confirm = true)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        if (!$shipment) {
            return '';
        }

        $magentoShipment = $shipment->getShipment();
        $packingSlip = $this->pdfShipment->create($magentoShipment);
        $packingSlip = $this->barcodeMerger->add($packingSlip, $magentoShipment);

        $pdfShipment = $this->pdfShipment;
        $this->mergeWithLabels->setY($pdfShipment->getY());

        if ($withLabels) {
            $packingSlip = $this->mergeWithLabels($shipmentId, $packingSlip, $confirm);
        }

        return $packingSlip;
    }

    /**
     * @param int    $shipmentId
     * @param string $packingslip
     * @param bool   $confirm
     *
     * @return string
     */
    private function mergeWithLabels($shipmentId, $packingslip, $confirm)
    {
        $showLabel = $this->labelAndPackingslipOptions->getShowLabel();

        switch ($showLabel) {
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_TOGETHER:
                return $this->mergeWithLabels->merge($shipmentId, $packingslip, true, $confirm);
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_SEPARATE:
                return $this->mergeWithLabels->merge($shipmentId, $packingslip, false, $confirm);
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_NONE:
            default:
                return $packingslip;
        }
    }
}
