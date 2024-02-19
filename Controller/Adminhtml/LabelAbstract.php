<?php

namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Helper\Tracking\Track;
use \Magento\Sales\Model\Order\Shipment;

//@codingStandardsIgnoreFile
abstract class LabelAbstract extends Action
{
    /**
     * @var GetLabels
     */
    //@codingStandardsIgnoreLine
    protected $getLabels;

    /**
     * @var GetPdf
     */
    //@codingStandardsIgnoreLine
    protected $getPdf;

    /**
     * @var array
     */
    //@codingStandardsIgnoreLine
    protected $labels = [];

    /**
     * @var BarcodeHandler
     */
    //@codingStandardsIgnoreLine
    protected $barcodeHandler;

    /**
     * @var Track
     */
    //@codingStandardsIgnoreLine
    protected $track;

    /**
     * @var array
     */
    //@codingStandardsIgnoreLine
    protected $stateToHandel = ['new', 'processing'];

    /**
     * @var GetPackingslip
     */
    private $getPackingSlip;

    /**
     * @param Context        $context
     * @param GetLabels      $getLabels
     * @param GetPdf         $getPdf
     * @param GetPackingslip $getPackingSlip
     * @param BarcodeHandler $barcodeHandler
     * @param Track          $track
     */
    //@codingStandardsIgnoreLine
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        GetPackingslip $getPackingSlip,
        BarcodeHandler $barcodeHandler,
        Track $track
    ) {
        parent::__construct($context);

        $this->getLabels      = $getLabels;
        $this->getPdf         = $getPdf;
        $this->getPackingSlip = $getPackingSlip;
        $this->barcodeHandler = $barcodeHandler;
        $this->track          = $track;
    }

    /**
     * @param $shipmentId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    //@codingStandardsIgnoreLine
    protected function setLabel($shipmentId)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return $this->messageManager->addErrorMessage(__('[POSTNL-0070] - Unable to generate barcode for shipment #%1.', $shipmentId));
        }

        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * @param      $shipmentId
     * @param bool $withLabels
     * @param bool $confirm
     */
    //@codingStandardsIgnoreLine
    protected function setPackingslip($shipmentId, $withLabels = true, $confirm = true)
    {
        $packingslip = $this->getPackingSlip->get($shipmentId, $withLabels, $confirm);
        if (is_array($packingslip) && isset($packingslip['errors'])) {
            $this->getLabels->handleRequestMessages($packingslip['errors']);
            return;
        }

        if (strlen((string)$packingslip) === 0) {
            return;
        }

        $this->labels[] = $packingslip;
    }

    /**
     * @param $shipment
     */
    //@codingStandardsIgnoreLine
    protected function setTracks($shipment)
    {
        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }
    }
}
