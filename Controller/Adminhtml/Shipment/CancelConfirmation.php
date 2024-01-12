<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use TIG\PostNL\Service\Shipment\ShipmentService;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class CancelConfirmation extends Action
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
        
        $this->shipmentService = $shipmentService;
        $this->resetService    = $resetService;
    }
    
    /**
     * When you change the consignment confirmation,
     * all the associated elements in question will be removed from the shipment.
     * After that, new information like the shipping address can be set, before re-confirming the consignment.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute()
    {
        $this->postNLShipmentId = $this->getRequest()->getParam('postnl_shipment_id');
        $this->shipmentId       = $this->getRequest()->getParam('shipment_id');
        
        $this->resetService->resetShipment($this->shipmentId);
        
        $resultDirect = $this->resultRedirectFactory->create();
        
        return $resultDirect->setPath(
            'sales/shipment/view',
            ['shipment_id' => $this->shipmentId]
        );
    }
}
