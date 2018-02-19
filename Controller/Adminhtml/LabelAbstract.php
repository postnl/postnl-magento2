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
namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\AdminHtml\PdfDownload as GetPdf;
use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;

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
     * @var $shipment
     */
    //@codingStandardsIgnoreLine
    protected $getPackingSlip;

    /**
     * @param Context       $context
     * @param GetLabels     $getLabels
     * @param GetPdf        $getPdf
     * @param PdfShipment   $getPackingSlip
     */
    //@codingStandardsIgnoreLine
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        PdfShipment $getPackingSlip
    ) {
        parent::__construct($context);

        $this->getLabels  = $getLabels;
        $this->getPdf     = $getPdf;
        $this->getPackingSlip   = $getPackingSlip;
    }

    /**
     * @param $shipmentId
     */
    //@codingStandardsIgnoreLine
    protected function setLabel($shipmentId)
    {
        $labels = $this->getLabels->get($shipmentId);

        if (empty($labels)) {
            return;
        }

        $this->labels = array_merge($this->labels, $labels);
    }

    /**
     * @param $shipments
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function generatePackingSlips($shipments)
    {
        $packingSlips = $this->getPackingSlip->getPdf($shipments)->render();
        return $this->getPdf->get($packingSlips, GetPdf::FILETYPE_PACKINGSLIP);
    }
}
