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

        $this->setRequestData($shipmentId);
        $this->confirm();
        $this->setConfirmedAt($shipmentId);
        $this->setTrack($shipmentId);

        $this->messageManager->addComplexSuccessMessage(
        // @codingStandardsIgnoreLine
            __('Shipment successfully confirmed')->getText()
        );

        $resultDirect = $this->resultRedirectFactory->create();
        return $resultDirect->setPath('sales/shipment/view', ['shipment_id' => $shipmentId]);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|mixed|\stdClass
     */
    private function confirm()
    {
        try {
            return $this->confirming->call();
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('Could not confirm shipment : %1', $exception->getLogMessage())->getText()
            );
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
    }

    /**
     * @param int $shipmentId
     *
     */
    private function setRequestData($shipmentId)
    {
        $postNLShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);
        $this->confirming->setParameters($postNLShipment);
    }

    /**
     * @param $shipmentId
     */
    private function setConfirmedAt($shipmentId)
    {
        $postNLShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);
        $postNLShipment->setConfirmedAt($this->helper->getDate());
        $this->postnlShipmentRepository->save($postNLShipment);
    }

    /**
     * @param int $shipmentId
     */
    private function setTrack($shipmentId)
    {
        $shipment = $this->shipmentRepository->get($shipmentId);
        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }
    }
}
