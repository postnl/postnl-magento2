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

use TIG\PostNL\Api\ShipmentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate as ShippingDateRenderer;

class ShippingDate extends AbstractGrid
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var array
     */
    private $shipments = [];

    /**
     * @var ShippingDateRenderer
     */
    private $shippingDateRenderer;

    /**
     * @param ContextInterface            $context
     * @param UiComponentFactory          $uiComponentFactory
     * @param ShipmentRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param ShippingDateRenderer        $shippingDateRenderer
     * @param array                       $components
     * @param array                       $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShipmentRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShippingDateRenderer $shippingDateRenderer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentRepository = $orderRepository;
        $this->shippingDateRenderer = $shippingDateRenderer;
    }

    /**
     * Preload all the needed models in 1 query.
     */
    // @codingStandardsIgnoreLine
    protected function prepareData()
    {
        $orderIds = $this->collectIds();
        $models = $this->loadModels($orderIds);

        foreach ($models as $model) {
            $this->shipments[$model->getOrderId()][] = $model;
        }
    }

    /**
     * @param object $item
     *
     * @return null|string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $entityId = $item['entity_id'];
        if (!array_key_exists($entityId, $this->shipments)) {
            return null;
        }

        $output = '';
        /** @var \TIG\PostNL\Model\Shipment $model */
        foreach ($this->shipments[$entityId] as $model) {
            $output .= $this->shippingDateRenderer->render($model) . '<br>';
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
     * @param array $orderIds
     *
     * @return \TIG\PostNL\Model\Shipment[]
     */
    private function loadModels($orderIds = [])
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_id', $orderIds, 'IN');

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->shipmentRepository->getList($searchCriteria->create());

        return $list->getItems();
    }
}
