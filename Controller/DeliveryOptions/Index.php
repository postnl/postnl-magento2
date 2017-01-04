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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;
use TIG\PostNL\Webservices\Endpoints\Locations;
use \Magento\Checkout\Model\Session;
use TIG\PostNL\Helper\AddressEnhancer;

/**
 * Class Index
 *
 * @package TIG\PostNL\Controller\DeliveryOptions
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var DeliveryDate
     */
    private $deliveryEndpoint;

    /**
     * @var  TimeFrame
     */
    private $timeFrameEndpoint;

    /**
     * @var  Locations
     */
    private $locationsEndpoint;

    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var
     */
    private $checkoutSession;

    /**
     * @param Context           $context
     * @param PageFactory       $resultPageFactory
     * @param Data              $jsonHelper
     * @param DeliveryDate      $deliveryDate
     * @param TimeFrame         $timeFrame
     * @param Locations         $locations
     * @param Session           $checkoutSession
     * @param AddressEnhancer   $addressEnhancer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame,
        Locations $locations,
        Session $checkoutSession,
        AddressEnhancer $addressEnhancer
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper        = $jsonHelper;
        $this->deliveryEndpoint  = $deliveryDate;
        $this->timeFrameEndpoint = $timeFrame;
        $this->locationsEndpoint = $locations;
        $this->checkoutSession   = $checkoutSession;
        $this->addressEnhancer   = $addressEnhancer;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['type']) || !isset($params['address'])) {
            return $this->jsonResponse(__('No Address data found or no Type specified'));
        }

        try {
            return $this->jsonResponse($this->getDataBasedOnType($params['type'], $params['address']));
        } catch (LocalizedException $exception) {
            return $this->jsonResponse($exception->getMessage());
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage());
        }
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

    /**
     * @param $address
     *
     * @return \Magento\Framework\Phrase
     */
    private function getNearestLocations($address)
    {
        if (!$this->checkoutSession->getPostNLDeliveryDate()) {
            $this->getDeliveryDay($address);
        }

        return $this->getLocations($address);
    }

    /**
     * @param $address
     *
     * @return array
     */
    private function getPosibleDeliveryDays($address)
    {
        $startDate  = $this->getDeliveryDay($address);
        $timeFrames = $this->getTimeFrames($address, $startDate);
        // Filter the time frames so we can use them in knockoutJS.
        return $this->timeFrameEndpoint->filterTimeFrames($timeFrames);
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @param array $address
     *
     * @return array
     */
    private function getDeliveryDay($address)
    {
        $this->deliveryEndpoint->setParameters($address);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }

    /**
     * @param $address
     *
     * @return \Magento\Framework\Phrase
     */
    private function getLocations($address)
    {
        $this->locationsEndpoint->setParameters($address, $this->checkoutSession->getPostNLDeliveryDate());
        $response = $this->locationsEndpoint->call();
        //@codingStandardsIgnoreLine
        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            return __('Invalid GetLocationsResult response: %1', var_export($response, true));
        }

        //@codingStandardsIgnoreLine
        return $response->GetLocationsResult->ResponseLocation;
    }

    /**
     * CIF call to get the timeframes.
     * @param $address
     * @param $startDate
     *
     * @return \Magento\Framework\Phrase
     */
    private function getTimeFrames($address, $startDate)
    {
        $this->timeFrameEndpoint->setParameters($address, $startDate);
        $response = $this->timeFrameEndpoint->call();
        //@codingStandardsIgnoreLine
        if (!is_object($response) || !isset($response->Timeframes->Timeframe)) {
            return __('Invalid GetTimeframes response: %1', var_export($response, true));
        }

        //@codingStandardsIgnoreLine
        return $response->Timeframes->Timeframe;
    }

    /**
     * @param $type
     * @param $address
     * @return array|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\Phrase
     */
    private function getDataBasedOnType($type, $address)
    {
        $this->addressEnhancer->set($address);

        if ($type == 'deliverydays') {
            return $this->getPosibleDeliveryDays($this->addressEnhancer->get());
        }

        if ($type == 'locations') {
            return $this->getNearestLocations($this->addressEnhancer->get());
        }

        //@codingStandardsIgnoreLine
        return $this->jsonResponse(__('Incorrect Type specified'));
    }
}
