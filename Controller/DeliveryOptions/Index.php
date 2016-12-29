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

/**
 * Class Index
 *
 * @package TIG\PostNL\Controller\DeliveryOptions
 */
class Index extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var Data */
    protected $jsonHelper;

    /** @var DeliveryDate */
    protected $deliveryEndpoint;

    /** @var  TimeFrame */
    protected $timeFrameEndpoint;

    /** @var  Locations */
    protected $locationsEndpoint;

    /** @var */
    protected $checkoutSession;

    /**
     * @param Context      $context
     * @param PageFactory  $resultPageFactory
     * @param Data         $jsonHelper
     * @param DeliveryDate $deliveryDate
     * @param TimeFrame    $timeFrame
     * @param Locations    $locations
     * @param Session      $checkouSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame,
        Locations $locations,
        Session $checkouSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper        = $jsonHelper;
        $this->deliveryEndpoint  = $deliveryDate;
        $this->timeFrameEndpoint = $timeFrame;
        $this->locationsEndpoint = $locations;
        $this->checkoutSession   = $checkouSession;
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

        $params['address'] = [
            'country' => 'NL',
            'postcode' => '1014 BA',
            'street'   => 'Kabelweg 37'
        ];

        if (!isset($params['address'])) {
            return $this->jsonResponse(__('No Address data found'));
        }

        if (!isset($params['type'])) {
            return $this->jsonResponse(__('No Type specified'));
        }

        switch ($params['type']) {
            case 'deliverydays' :
                $data = $this->getPosibleDeliveryDays($params['address']);
                break;
            case 'locations' :
                $data = $this->getNearestLocations($params['address']);
                break;
            default :
                return $this->jsonResponse(__('Incorrect Type specified'));
        }

        try {
            return $this->jsonResponse($data);
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
    protected function getNearestLocations($address)
    {
        if (!$this->getDeliveryDate()) {
            $this->getDeliveryDay($address);
        }

        $locations = $this->getLocations($address);

        return $locations;
    }

    /**
     * @param $address
     *
     * @return array
     */
    protected function getPosibleDeliveryDays($address)
    {
        $startDate  = $this->getDeliveryDay($address);
        $timeFrames = $this->getTimeFrames($address, $startDate);

        // Filter the time frames so we can use them in knockoutJS.
        return $this->timeFrameEndpoint->filterTimeFrames($timeFrames, $address['country']);
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @param array $address
     *
     * @return array
     */
    protected function getDeliveryDay($address)
    {
        $this->deliveryEndpoint->setParameters($address);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }
        $this->setDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }

    /**
     * @param $address
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getLocations($address)
    {
        $this->locationsEndpoint->setParameters($address, $this->getDeliveryDate());
        $response = $this->locationsEndpoint->call();

        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            return __('Invalid GetLocationsResult response: %1', var_export($response, true));
        }

        return $response->GetLocationsResult->ResponseLocation;
    }

    /**
     * CIF call to get the timeframes.
     * @param $address
     * @param $startDate
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getTimeFrames($address, $startDate)
    {
        $this->timeFrameEndpoint->setParameters($address, $startDate);
        $response = $this->timeFrameEndpoint->call();

        if (!is_object($response) || !isset($response->Timeframes->Timeframe)) {
            return __('Invalid GetTimeframes response: %1', var_export($response, true));
        }

        return $response->Timeframes->Timeframe;
    }

    /**
     * @param $startDate
     */
    protected function setDeliveryDate($startDate)
    {
        $this->checkoutSession->setPostNLDeliveryDate($startDate);
    }

    /**
     * @return mixed
     */
    protected function getDeliveryDate()
    {
        return $this->checkoutSession->getPostNLDeliveryDate();
    }
}
