<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use TIG\PostNL\Exception;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Endpoints\Confirming;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Helper\Tracking\Track;

class ConfirmShipping extends Action
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
     * @var Confirming
     */
    private $confirming;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $postnlShipmentRepository;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Context $context
     * @param ShipmentRepository $shipmentRepository
     * @param Track $track
     * @param Confirming $confirming
     * @param ShipmentRepositoryInterface $repositoryInterface
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        Track $track,
        Confirming $confirming,
        ShipmentRepositoryInterface $repositoryInterface,
        Data $helper
    ) {
        parent::__construct($context);

        $this->shipmentRepository = $shipmentRepository;
        $this->track = $track;
        $this->confirming = $confirming;
        $this->postnlShipmentRepository = $repositoryInterface;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $this->confirm($shipmentId);

        $resultDirect = $this->resultRedirectFactory->create();
        return $resultDirect->setPath('sales/shipment/view', ['shipment_id' => $shipmentId]);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|mixed|\stdClass
     */
    private function confirm($shipmentId)
    {
        try {
            $this->updateRequestData($shipmentId);
            $this->confirming->call();
            $this->updateConfirmedAt($shipmentId);
            $this->insertTrack($shipmentId);

            $this->messageManager->addSuccessMessage(
            // @codingStandardsIgnoreLine
                __('Shipment successfully confirmed')->getText()
            );
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('Could not confirm shipment: %1', $exception->getLogMessage())->getText()
            );
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
    }

    /**
     * @param int $shipmentId
     *
     */
    // @codingStandardsIgnoreLine
    private function updateRequestData($shipmentId)
    {
        $postNLShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);
        $this->confirming->setParameters($postNLShipment);
    }

    /**
     * @param $shipmentId
     */
    private function updateConfirmedAt($shipmentId)
    {
        $postNLShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);
        $postNLShipment->setConfirmedAt($this->helper->getDate());
        $postNLShipment->setConfirmed(true);
        $this->postnlShipmentRepository->save($postNLShipment);
    }

    /**
     * @param int $shipmentId
     */
    private function insertTrack($shipmentId)
    {
        $shipment = $this->shipmentRepository->get($shipmentId);
        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }
    }
}
