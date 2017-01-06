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
namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Validators\DeliveryOptions as OptionsValidator;

/**
 * Class Save
 *
 * @package TIG\PostNL\Controller\DeliveryOptions
 */
class Save extends Action
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var OptionsValidator
     */
    private $validator;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Data $jsonHelper,
        OptionsValidator $validator
    ) {
        $this->orderFactory    = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->jsonHelper      = $jsonHelper;
        $this->validator       = $validator;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $this->validator->hasAllRequiredOrderParams($params);
        $saved  = $this->saveDeliveryOption($params);

        try {
            return $this->jsonResponse($saved);
        } catch (LocalizedException $exception) {
            return $this->jsonResponse($exception->getMessage());
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage());
        }
    }

    /**
     * @param $params
     *
     * @return \Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveDeliveryOption($params)
    {
        // Get PostNL order
        $postnlOrder = $this->getPostNLOrder($params['quote_id']);

        if (null === $postnlOrder->getId()) {
            $postnlOrder->setData('quote_id', $params['quote_id']);
        }

        $postnlOrder->setData('delivery_date', $params['delivery_date']);
        $postnlOrder->setData('expected_delivery_time_start', $params['expected_delivery_time_start']);
        $postnlOrder->setData('expected_delivery_time_end', $params['expected_delivery_time_end']);
        $postnlOrder->setData('is_pakjegemak', $params['is_pakjegemak']);
        $postnlOrder->setData('pg_location_code', $params['pg_location_code']);
        $postnlOrder->setData('pg_retail_network_id', $params['pg_retail_network_id']);

        $this->orderRepository->save($postnlOrder);

        return __('ok');
    }

    /**
     * @param $quoteId
     *
     * @return \TIG\PostNL\Model\Order
     */
    private function getPostNLOrder($quoteId)
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

    /**
     * Create json response
     *
     * @param string $response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    //@codingStandardsIgnoreLine
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}