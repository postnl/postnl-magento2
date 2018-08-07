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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use TIG\PostNL\Service\Shipment\ShipmentService;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use TIG\PostNL\Model\Shipment as PostNLShipment;

class ChangeConfirmation extends Action
{
    /**
     * @var ShipmentService
     */
    private $shipmentService;

    /**
     * @var ResetPostNLShipment
     */
    private $resetService;

    /**
     * @var int
     */
    private $postNLShipmentId;

    /**
     * @var int
     */
    private $shipmentId;

    /**
     * @param Context         $context
     * @param ShipmentService $shipmentService

     */
    public function __construct(
        Context $context,
        ShipmentService $shipmentService,
        ResetPostNLShipment $resetService
    ) {
        parent::__construct($context);

        $this->shipmentService      = $shipmentService;
        $this->resetService = $resetService;
    }

    /**
     * When you change the consignment confirmation,
     * all the associated elements in question will be removed from the shipment.
     * After that, new information like the shipping address can be set, before re-confirming the consignment.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->postNLShipmentId = $this->getRequest()->getParam('postnl_shipment_id');
        $this->shipmentId       = $this->getRequest()->getParam('shipment_id');

        $this->resetShipment();
        $this->resetService->resetShipment($this->shipmentId);

        $resultDirect = $this->resultRedirectFactory->create();
        return $resultDirect->setPath(
            'sales/shipment/view',
            ['shipment_id' => $this->shipmentId]
        );
    }

    /**
     * Resets the confirmation date to null.
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function resetShipment()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->shipmentService->getPostNLShipment($this->postNLShipmentId);
        $postNLShipment->setConfirmedAt(null);
        $postNLShipment->setMainBarcode(null);
        $this->shipmentService->save($postNLShipment);
    }
}
