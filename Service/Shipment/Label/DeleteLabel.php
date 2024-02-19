<?php

namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Model\ShipmentLabelRepository;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\ShipmentServiceAbstract;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

class DeleteLabel extends ShipmentServiceAbstract
{
    /**
     * @var ShipmentLabelRepository
     */
    private $shipmentLabelRepository;

    /**
     * @param ShipmentLabelRepository $shipmentLabelRepository
     * @param Log                       $log
     * @param PostNLShipmentRepository  $postNLShipmentRepository
     * @param ShipmentRepository        $shipmentRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentLabelRepository $shipmentLabelRepository,
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $log,
            $postNLShipmentRepository,
            $shipmentRepository,
            $searchCriteriaBuilder
        );

        $this->shipmentLabelRepository = $shipmentLabelRepository;
    }

    /**
     * Deletes one single label.
     *
     * @param ShipmentLabelInterface $label
     */
    public function delete($label)
    {
        try {
            $this->shipmentLabelRepository->delete($label);
        } catch (PostNLException $exception) {
            $this->logger->alert('Can\'t delete shipment label', $exception->getLogMessage());
        }
    }

    /**
     * Deletes all labels associated to the PostNL Shipment ID.
     *
     * @param $postNLShipmentId
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteAllByParentId($postNLShipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'parent_id',
            $postNLShipmentId
        );

        $labels = $this->shipmentLabelRepository->getList($searchCriteria->create());

        /** @var ShipmentLabelInterface $label */
        foreach ($labels->getItems() as $label) {
            // @codingStandardsIgnoreLine
            $this->delete($label);
        }
    }
}
