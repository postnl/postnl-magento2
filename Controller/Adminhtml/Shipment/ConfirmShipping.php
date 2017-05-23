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
use TIG\PostNL\Webservices\Endpoints\Confirming;
use Magento\Sales\Model\Order\Shipment;
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
     * @param Context                     $context
     * @param ShipmentRepository          $shipmentRepository
     * @param Track                       $track
     * @param Confirming                  $confirming
     * @param ShipmentRepositoryInterface $repositoryInterface
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        Track $track,
        Confirming $confirming,
        ShipmentRepositoryInterface $repositoryInterface
    ) {
        parent::__construct($context);

        $this->shipmentRepository = $shipmentRepository;
        $this->track = $track;
        $this->confirming = $confirming;
        $this->postnlShipmentRepository = $repositoryInterface;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        $this->setRequestData($shipmentId);

        try {
            $this->confirming->call();
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
            // @codingStandardsIgnoreLine
                __('Could not confirm shipment : %1', $e->getLogMessage())
            );
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
        $this->setTrack($shipmentId);
        $this->messageManager->addComplexSuccessMessage(
        // @codingStandardsIgnoreLine
            __('Shipment successfully confirmed')
        );

        $resultDirect = $this->resultRedirectFactory->create();
        return $resultDirect->setPath('sales/shipment/view', ['shipment_id' => $shipmentId]);

    }

    /**
     * @param int $shipmentId
     *
     * @return bool
     */
    private function setRequestData($shipmentId)
    {
        $postNLShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);
        $this->confirming->setParameters($postNLShipment);
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
