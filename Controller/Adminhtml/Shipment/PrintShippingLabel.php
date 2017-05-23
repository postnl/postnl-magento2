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
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;

use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;

class PrintShippingLabel extends LabelAbstract
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var Track
     */
    private $track;

    /**
     * @var BarcodeHandler
     */
    private $barcodeHandler;

    /**
     * @param Context            $context
     * @param GetLabels          $getLabels
     * @param GetPdf             $getPdf
     * @param ShipmentRepository $shipmentRepository
     * @param Track              $track
     * @param BarcodeHandler     $barcodeHandler
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        ShipmentRepository $shipmentRepository,
        Track $track,
        BarcodeHandler $barcodeHandler
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf
        );

        $this->shipmentRepository = $shipmentRepository;
        $this->track              = $track;
        $this->barcodeHandler     = $barcodeHandler;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $labels = $this->getLabels();

        if (empty($labels)) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information')
            );

            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        return $this->getPdf->get($labels);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    private function getShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        return $this->shipmentRepository->get($shipmentId);
    }

    /**
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    private function getLabels()
    {
        $shipment = $this->getShipment();
        $shippingAddress = $shipment->getShippingAddress();
        $this->barcodeHandler->prepareShipment($shipment->getId(), $shippingAddress->getCountryId());

        $labels = $this->getLabels->get($shipment->getId(), false);

        return $labels;
    }
}
