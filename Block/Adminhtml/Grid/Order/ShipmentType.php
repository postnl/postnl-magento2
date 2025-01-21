<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Block\Adminhtml\Renderer\ShipmentType as Renderer;
use TIG\PostNL\Service\Shipping\LetterboxPackage;

class ShipmentType extends AbstractGrid
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ShipmentType
     */
    private $shipmentType;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @param ContextInterface              $context
     * @param UiComponentFactory            $uiComponentFactory
     * @param OrderRepositoryInterface      $orderRepository
     * @param Renderer                      $shipmentType
     * @param LetterboxPackage              $letterboxPackage
     * @param array                         $components
     * @param array                         $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        Renderer $shipmentType,
        LetterboxPackage $letterboxPackage,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->orderRepository = $orderRepository;
        $this->shipmentType    = $shipmentType;
        $this->letterboxPackage = $letterboxPackage;
    }

    /**
     * @param object $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $output = '';
        $order  = $this->orderRepository->getByOrderId($item['entity_id']);
        if (!$order) {
            return $output;
        }

        if ($order->getProductCode()) {
            $output = $this->shipmentType->render($order->getProductCode(), $order->getType());
        }

        try {
            if ($this->letterboxPackage->isPossibleLetterboxPackage($order)) {
                $output = 'Domestic<br><em class="possible-letterbox"
                       title="Standard shipment">Standard shipment (possible letterboxpackage)</em>';
            }

            return $output;
        } catch (\Exception $exception) {
            return $output;
        }
    }
}
