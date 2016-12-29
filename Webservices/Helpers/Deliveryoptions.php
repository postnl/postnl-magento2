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
namespace TIG\PostNL\Webservices\Helpers;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \TIG\PostNL\Helper\Data;
use \TIG\PostNL\Config\Provider\AccountConfiguration;
use \Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
use TIG\PostNL\Config\Provider\ShippingOptions;

/**
 * Class Deliveryoptions
 *
 * @package TIG\PostNL\Webservices\Helpers
 */
class Deliveryoptions extends AbstractHelper
{
    const PAKJEGEMAK_DELIVERY_OPTION         = 'PG';

    /** @var Data  */
    protected $postNLhelper;

    /** @var AccountConfiguration  */
    protected $accountConfig;

    protected $serverAddress;

    protected $shippingOptions;

    /**
     * @param Context              $context
     * @param Data                 $postNLhelper
     * @param AccountConfiguration $accountConfiguration
     * @param ServerAddress        $serverAddress
     * @param ShippingOptions      $shippingOptions
     */
    public function __construct(
        Context $context,
        Data $postNLhelper,
        AccountConfiguration $accountConfiguration,
        ServerAddress $serverAddress,
        ShippingOptions $shippingOptions
    ) {
        parent::__construct($context);

        $this->postNLhelper    = $postNLhelper;
        $this->accountConfig   = $accountConfiguration;
        $this->serverAddress   = $serverAddress;
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @todo : Add more types.
     *
     *  - const PAKJEGEMAK_EXPRESS_DELIVERY_OPTION = 'PGE';
     *  - const PAKKETAUTOMAAT_DELIVERY_OPTION     = 'PA';
     *
     * @return array
     */
    public function getAllowedDeliveryOptions()
    {
        $deliveryOptions = [];
        if ($this->shippingOptions->isPakjegemakActive()) {
            $deliveryOptions [] = self::PAKJEGEMAK_DELIVERY_OPTION;
        }

        return $deliveryOptions;
    }

    /**
     * @todo make correct and dynamic array (including configuration)
     * @return array
     */
    public function getDeliveryDatesOptions()
    {
        return [
            'Sunday',
            'Daytime',
            'Evening',
        ];
    }

    /**
     * @param $startDate
     *
     * @return string
     */
    public function getEndDate($startDate)
    {
        $maximumNumberOfDeliveryDays = 6;

        $endDate = new \DateTime($startDate, new \DateTimeZone('UTC'));
        $endDate->add(new \DateInterval("P{$maximumNumberOfDeliveryDays}D"));

        return $endDate->format('d-m-Y');
    }
}
