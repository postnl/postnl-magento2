<?php

namespace TIG\PostNL\Block\Adminhtml\Shipment;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Service\Options\ShipmentSupported;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionSource;

abstract class OptionsAbstract extends Template implements BlockInterface
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    // @codingStandardsIgnoreLine
    protected $order;

    /**
     * @var ShipmentSupported
     */
    // @codingStandardsIgnoreLine
    protected $productConfig;

    /**
     * @var ProductOptionSource
     */
    // @codingStandardsIgnoreLine
    protected $productSource;

    /**
     * @var OrderRepository
     */
    // @codingStandardsIgnoreLine
    protected $orderRepository;

    /**
     * @var Registry
     */
    // @codingStandardsIgnoreLine
    protected $registry;

    /**
     * @param Context $context
     * @param ShipmentSupported $productOptions
     * @param ProductOptionSource $productOptionsSource
     * @param OrderRepository $orderRepository
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        ShipmentSupported $productOptions,
        ProductOptionSource $productOptionsSource,
        OrderRepository $orderRepository,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productConfig   = $productOptions;
        $this->productSource   = $productOptionsSource;
        $this->orderRepository = $orderRepository;
        $this->registry        = $registry;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order
     */
    // @codingStandardsIgnoreLine
    protected function getOrder()
    {
        if ($this->order) {
            return $this->order;
        }

        $request = $this->getRequest();
        $orderId = (int)$request->getParam('order_id');
        if (!$orderId) {
            $orderId = $this->getShipment()->getOrderId();
        }

        return $this->order = $this->orderRepository->get($orderId);
    }

    /**
     * @return bool
     */
    public function getIsPostNLOrder()
    {
        $method = $this->getOrder()->getShippingMethod();
        return ($method == 'tig_postnl_regular');
    }

    /**
     * @return array
     */
    public function getProductOptions()
    {
        $supportedCodes = $this->productConfig->get($this->getOrder());
        $productOptions = [];
        foreach ($supportedCodes as $code) {
            $productOptions[$code] = $this->productSource->getOptionsByCode($code);
        }

        return $productOptions;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->registry->registry('current_shipment');
    }
}
