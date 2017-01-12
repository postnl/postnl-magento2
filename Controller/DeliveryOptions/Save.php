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

use TIG\PostNL\Controller\AbstractDeliveryOptions;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use \Magento\Checkout\Model\Session;
use TIG\PostNL\Helper\DeliveryOptions\PickupAddress;

/**
 * Class Save
 *
 * @package TIG\PostNL\Controller\DeliveryOptions
 */
class Save extends AbstractDeliveryOptions
{
    /**
     * @var OrderParams
     */
    private $orderParams;

    /**
     * @var PickupAddress
     */
    private $pickupAddress;

    /**
     * @param Context          $context
     * @param OrderFactory     $orderFactory
     * @param OrderRepository  $orderRepository
     * @param Data             $jsonHelper
     * @param OrderParams      $orderParams
     * @param Session          $checkoutSession
     * @param PickupAddress    $pickupAddress
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Data $jsonHelper,
        OrderParams $orderParams,
        Session $checkoutSession,
        PickupAddress $pickupAddress
    ) {
        $this->orderParams   = $orderParams;
        $this->pickupAddress = $pickupAddress;

        parent::__construct(
            $context,
            $jsonHelper,
            $orderFactory,
            $orderRepository,
            $checkoutSession
        );
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \TIG\PostNL\Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['type'])) {
            return $this->jsonResponse(__('No Type specified'));
        }

        $saved = $this->saveDeliveryOption($params);

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
        $params      = $this->addSessionDataToParams($params);
        $params      = $this->orderParams->get($params);
        $postnlOrder = $this->getPostNLOrderByQuoteId($params['quote_id']);

        foreach ($params as $key => $value) {
            $postnlOrder->setData($key, $value);
        }

        $this->orderRepository->save($postnlOrder);

        if ($params['type'] == 'pickup') {
            $this->pickupAddress->set($params['pg_address']);
        }

        return __('ok');
    }

    /**
     * @param $params
     *
     * @codingStandardsIgnoreLine
     * @todo : When type is pickup the delivery Date needs to be recalculated,
     *
     * @return mixed
     */
    private function addSessionDataToParams($params)
    {
        if (!isset($params['date']) && $params['type'] == 'pickup') {
            $params['date'] = $this->checkoutSession->getPostNLDeliveryDate();
        }

        $params['quote_id'] = $this->checkoutSession->getQuoteId();

        return $params;
    }
}
