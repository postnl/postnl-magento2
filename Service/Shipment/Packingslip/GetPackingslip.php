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
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ShowShippingLabel;

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
     * @var LabelAndPackingslipOptions
     */
    private $labelAndPackingslipOptions;

    /**
     * @var MergeWithLabels
     */
    private $mergeWithLabels;

    /**
     * GetPackingslip constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentLabelRepository
     * @param PdfShipment                 $pdfShipment
     * @param LabelAndPackingslipOptions  $labelAndPackingslipOptions
     * @param MergeWithLabels             $mergeWithLabels
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentLabelRepository,
        PdfShipment $pdfShipment,
        LabelAndPackingslipOptions $labelAndPackingslipOptions,
        MergeWithLabels $mergeWithLabels
    ) {
        $this->shipmentRepository = $shipmentLabelRepository;
        $this->pdfShipment = $pdfShipment;
        $this->labelAndPackingslipOptions = $labelAndPackingslipOptions;
        $this->mergeWithLabels = $mergeWithLabels;
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
        $packingSlip = $packingSlip->render();

        $pdfShipment = $this->pdfShipment;
        $currentYPosition = $pdfShipment->y;
        $this->mergeWithLabels->setY($currentYPosition);

        $packingSlipPdf = $this->mergeWithLabels($shipmentId, $packingSlip);

        return $packingSlipPdf;
    }

    /**
     * @param int    $shipmentId
     * @param string $packingslip
     *
     * @return string
     */
    private function mergeWithLabels($shipmentId, $packingslip)
    {
        $showLabel = $this->labelAndPackingslipOptions->getShowLabel();

        switch ($showLabel) {
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_TOGETHER:
                return $this->mergeWithLabels->mergeTogether($shipmentId, $packingslip);
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_SEPARATE:
                return $this->mergeWithLabels->mergeSeparate($shipmentId, $packingslip);
            case ShowShippingLabel::SHOW_SHIPPING_LABEL_NONE:
            default:
                return $packingslip;
        }
    }
}
