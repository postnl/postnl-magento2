<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class SaveMulticolli extends \Magento\Backend\App\Action
{
    /**
     * @var \TIG\PostNL\Api\ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * SaveMulticolli constructor.
     *
     * @param Action\Context                                   $context
     * @param \TIG\PostNL\Api\ShipmentRepositoryInterface      $shipmentRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \TIG\PostNL\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();

        $shipmentId = $this->getRequest()->getParam('shipmentId');
        $parcelCount = $this->getRequest()->getParam('parcelCount');

        $shipment = $this->shipmentRepository->getById($shipmentId);

        if (!$shipment->canChangeParcelCount()) {
            return $response->setData([
                'success' => false,
            ]);
        }

        $shipment->setParcelCount($parcelCount);
        $this->shipmentRepository->save($shipment);

        return $response->setData([
            'success' => true,
        ]);
    }
}
