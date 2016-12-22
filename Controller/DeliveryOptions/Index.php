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
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /** @var DeliveryDate */
    protected $deliveryEndpoint;

    /** @var  TimeFrame */
    protected $timeFrameEndpoint;

    /**
     * @param Context      $context
     * @param PageFactory  $resultPageFactory
     * @param Data         $jsonHelper
     * @param DeliveryDate $deliveryDate
     * @param TimeFrame    $timeFrame
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper        = $jsonHelper;
        $this->deliveryEndpoint  = $deliveryDate;
        $this->timeFrameEndpoint = $timeFrame;
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

        $type = 'none';
        if (isset($params['type'])) {
            $type = $params['type'];
        }

        switch ($type) {
            case 'deliverydays' :
                $data = $this->getPosibleDeliveryDays();
                break;
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

    protected function getPosibleDeliveryDays()
    {
        // Format example
        $days = [
            ['day' => 'Monday', 'date' => '19-12-2016'],
            ['day' => 'Sunday', 'date' => '25-12-2016'],
        ];
        return $days;
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @return array
     */
    protected function getDeliveryDay()
    {
        $this->deliveryEndpoint->setRequestData([]);
        $response = $this->deliveryEndpoint->getDeliveryDate();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return $this->jsonResponse(__('Invalid GetDeliveryDate response: %1', var_export($response, true)));
        }

        return $response->DeliveryDate;
    }


}
