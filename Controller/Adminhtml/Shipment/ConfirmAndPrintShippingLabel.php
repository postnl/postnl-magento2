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

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;

use TIG\PostNL\Service\Converter\CanaryIslandToIC;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class ConfirmAndPrintShippingLabel extends LabelAbstract
{
    /** @var ShipmentRepository */
    private $shipmentRepository;

    /** @var CanaryIslandToIC */
    private $canaryConverter;

    /**
     * ConfirmAndPrintShippingLabel constructor.
     *
     * @param Context                  $context
     * @param GetLabels                $getLabels
     * @param GetPdf                   $getPdf
     * @param ShipmentRepository       $shipmentRepository
     * @param Track                    $track
     * @param BarcodeHandler           $barcodeHandler
     * @param GetPackingslip           $pdfShipment
     * @param CanaryIslandToIC         $canaryConverter
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        ShipmentRepository $shipmentRepository,
        Track $track,
        BarcodeHandler $barcodeHandler,
        GetPackingslip $pdfShipment,
        CanaryIslandToIC $canaryConverter
    ) {
        parent::__construct(
            $context,
            $getLabels,
            $getPdf,
            $pdfShipment,
            $barcodeHandler,
            $track
        );
        $this->canaryConverter = $canaryConverter;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\Message\ManagerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Pdf_Exception
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
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        return $this->shipmentRepository->get($shipmentId);
    }

    /**
     * @return array|\Magento\Framework\Phrase|string|\TIG\PostNL\Api\Data\ShipmentLabelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getLabels()
    {
        $shipment = $this->getShipment();
        $countryId = $this->getCountryId($shipment);

        try {
            $this->barcodeHandler->prepareShipment($shipment->getId(), $countryId, false);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(
                __('[POSTNL-0070] - Unable to generate barcode for shipment #%1', $shipment->getIncrementId())
            );
            return [];
        }

        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }

        $labels = $this->getLabels->get($shipment->getId());

        return $labels;
    }

    /**
     * @param Shipment $shipment
     *
     * @return mixed|string
     */
    private function getCountryId($shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();
        if ($shippingAddress->getCountryId() === 'ES') {
            $shippingAddress = $this->canaryConverter->convert($shippingAddress);
        }

        return $shippingAddress->getCountryId();
    }
}
