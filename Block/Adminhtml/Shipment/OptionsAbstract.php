<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
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

/**
 * Class OptionsAbstract
 *
 * @package TIG\PostNL\Block\Adminhtml\Shipment
 */
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

        $request     = $context->getRequest();
        $orderId     = (int)$request->getParam('order_id');
        if (!$orderId) {
            $orderId = $this->getShipment()->getOrderId();
        }

        $this->order = $this->orderRepository->get($orderId);
    }

    /**
     * @return bool
     */
    public function getIsPostNLOrder()
    {
        $method = $this->order->getShippingMethod();
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
