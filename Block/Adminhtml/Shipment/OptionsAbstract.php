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
namespace TIG\PostNL\Block\Adminhtml\Shipment;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionSource;

abstract class OptionsAbstract extends Template implements BlockInterface
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    // @codingStandardsIgnoreLine
    protected $order;

    /**
     * @var ProductOptions
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
     * @param ProductOptions $productOptions
     * @param ProductOptionSource $productOptionsSource
     * @param OrderRepository $orderRepository
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductOptions $productOptions,
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
        $supportedCodes = $this->productConfig->getSupportedProductOptions();
        $productOptions = [];
        foreach (explode(',', $supportedCodes) as $code) {
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
