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
     * @return string
     */
    public function get($shipmentId, $withLabels = true, $confirm = true)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        if (!$shipment) {
            return '';
        }

        $magentoShipment = $shipment->getShipment();
        $packingSlip = $this->pdfShipment->create($magentoShipment, $withLabels);
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
