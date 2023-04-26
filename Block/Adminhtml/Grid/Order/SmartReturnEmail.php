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
namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use TIG\PostNL\Block\Adminhtml\Renderer\SmartReturnEmail as Renderer;

class SmartReturnEmail extends AbstractGrid
{
    /**
     * @var Renderer
     */
    private $smartReturnEmail;

    /**
     * @var array
     */
    private $shipments = [];

    /**
     * @var $orders
     */
    private $orders;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ContextInterface            $context
     * @param UiComponentFactory          $uiComponentFactory
     * @param Renderer                    $smartReturnEmail
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepositoryInterface    $orderRepository
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param array                       $components
     * @param array                       $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Renderer $smartReturnEmail,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentRepository    = $shipmentRepository;
        $this->orderRepository       = $orderRepository;
        $this->smartReturnEmail      = $smartReturnEmail;
    }

    /**
     * Preload all the needed models in 1 query.
     */
    // @codingStandardsIgnoreLine
    protected function prepareData()
    {
        $orderIds = $this->collectIds();
        if (!$orderIds) {
            return null;
        }

        $searchCriteria = $this->createIdInSearchCriteria($orderIds);
        $models         = $this->loadShipments($searchCriteria);
        foreach ($models as $model) {
            $this->shipments[$model->getOrderId()][] = $model;
        }

        $models = $this->loadOrders($searchCriteria);
        foreach ($models as $model) {
            $this->orders[$model->getOrderId()] = $model;
        }
    }

    /**
     * @param object $item
     *
     * @return null|string
     */
    //@codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $entityId = $item['entity_id'];
        $output = '';

        if (array_key_exists($entityId, $this->shipments)) {
            /** @var \TIG\PostNL\Model\Shipment $model */
            foreach ($this->shipments[$entityId] as $model) {
                $output .= $this->smartReturnEmail->render($model) . '<br>';
            }
            return $output;
        }

        if (isset($this->orders[$entityId])) {
            $postnlOrder = $this->orders[$entityId];
            return $this->smartReturnEmail->render($postnlOrder->getShipmentId());
        }

        return $output;
    }

    /**
     * @return array
     */
    private function collectIds()
    {
        $orderIds = [];

        foreach ($this->items as $item) {
            $orderIds[] = $item['entity_id'];
        }

        return $orderIds;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteria
     *
     * @return \TIG\PostNL\Model\Shipment[]
     */
    private function loadShipments($searchCriteria)
    {
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->shipmentRepository->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteria
     *
     * @return \TIG\PostNL\Model\Order[]
     */
    private function loadOrders($searchCriteria)
    {
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->orderRepository->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * @param $orderIds
     *
     * @return \Magento\Framework\Api\SearchCriteria
     */
    private function createIdInSearchCriteria($orderIds = [])
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderIds, 'IN');

        return $searchCriteria->create();
    }
}
