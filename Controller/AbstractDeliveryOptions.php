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
namespace TIG\PostNL\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Json\Helper\Data;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use \Magento\Checkout\Model\Session;

/**
 * Class AbstractDeliveryOptions
 *
 * @package TIG\PostNL\Controller
 */
abstract class AbstractDeliveryOptions extends Action
{
    /**
     * @var Data
     */
    //@codingStandardsIgnoreLine
    protected $jsonHelper;

    /**
     * @var OrderFactory
     */
    //@codingStandardsIgnoreLine
    protected $orderFactory;

    /**
     * @var OrderRepository
     */
    //@codingStandardsIgnoreLine
    protected $orderRepository;

    /**
     * @var Session
     */
    //@codingStandardsIgnoreLine
    protected $checkoutSession;

    /**
     * @param Context         $context
     * @param Data            $jsonHelper
     * @param OrderFactory    $orderFactory
     * @param OrderRepository $orderRepository
     * @param Session         $checkoutSession
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Session $checkoutSession
    ) {
        $this->jsonHelper      = $jsonHelper;
        $this->orderFactory    = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context);
    }

    /**
     * Create json response
     *
     * @param string $response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    //@codingStandardsIgnoreLine
    protected function jsonResponse($response = '', $code = null)
    {
        $response = $this->getResponse();

        if ($code !== null) {
            $response->setStatusCode($code);
        }

        return $response->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * @param $quoteId
     *
     * @return \TIG\PostNL\Model\Order
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLOrderByQuoteId($quoteId)
    {
        /** @var \TIG\PostNL\Model\Order $postnlOrder */
        $postnlOrder = $this->orderFactory->create();

        /** @var \TIG\PostNL\Model\ResourceModel\Order\Collection $collection */
        $collection = $postnlOrder->getCollection();
        $collection->addFieldToFilter('quote_id', $quoteId);

        // @codingStandardsIgnoreLine
        $postnlOrder = $collection->setPageSize(1)->getFirstItem();

        return $postnlOrder;
    }
}
