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

use Magento\Framework\App\Response\Http;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use \Magento\Checkout\Model\Session;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

/**
 * Class Pickup
 *
 * @package TIG\PostNL\Controller\DeliveryOptions
 */
class Pickup extends AbstractDeliveryOptions
{
    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var  LocationsEndpoint
     */
    private $locationsEndpoint;

    /**
     * @var DeliveryDate
     */
    private $deliveryEndpoint;

    /**
     * @param Context           $context
     * @param OrderFactory      $orderFactory
     * @param OrderRepository   $orderRepository
     * @param Data              $jsonHelper
     * @param Session           $checkoutSession
     * @param AddressEnhancer   $addressEnhancer
     * @param LocationsEndpoint $locations
     * @param DeliveryDate      $deliveryDate
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        OrderRepository $orderRepository,
        Data $jsonHelper,
        Session $checkoutSession,
        AddressEnhancer $addressEnhancer,
        LocationsEndpoint $locations,
        DeliveryDate $deliveryDate
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->locationsEndpoint = $locations;
        $this->deliveryEndpoint  = $deliveryDate;

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
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['address'])) {
            return $this->jsonResponse(__('No Address data found.'));
        }

        $this->addressEnhancer->set($params['address']);

        try {
            return $this->jsonResponse($this->getLocations($this->addressEnhancer->get()));
        } catch (LocalizedException $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        }
    }

    /**
     * @param $address
     *
     * @return \Magento\Framework\Phrase
     */
    private function getLocations($address)
    {
        $deliveryDate = false;
        if ($this->getDeliveryDay($address)) {
            $deliveryDate = $this->getDeliveryDay($address);
        }

        $this->locationsEndpoint->setParameters($address, $deliveryDate);
        $response = $this->locationsEndpoint->call();
        //@codingStandardsIgnoreLine
        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            return __('Invalid GetLocationsResult response: %1', var_export($response, true));
        }

        //@codingStandardsIgnoreLine
        return $response->GetLocationsResult->ResponseLocation;
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @param array $address
     *
     * @return array
     */
    private function getDeliveryDay($address)
    {
        if ($this->checkoutSession->getPostNLDeliveryDate()) {
            return $this->checkoutSession->getPostNLDeliveryDate();
        }

        $this->deliveryEndpoint->setParameters($address);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }
}
