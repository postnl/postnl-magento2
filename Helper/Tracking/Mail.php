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
namespace TIG\PostNL\Helper\Tracking;

use \TIG\PostNL\Helper\AbstractTracking;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \TIG\PostNL\Helper\Data as PostNLHelper;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order\ShipmentRepository;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use \Magento\Framework\Mail\TransportInterface;
use \Magento\Framework\Exception\MailException;
use \Magento\Sales\Model\Order\Shipment;
use \TIG\PostNL\Config\Provider\Webshop;
use \TIG\PostNL\Logging\Log;

/**
 * Class Mail
 *
 * @package TIG\PostNL\Helper\Tracking
 */
class Mail extends AbstractTracking
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var PostNLHelper
     */
    private $postNLHelperData;

    /**
     * @var TransportInterface
     */
    private $trackAndTraceEmail;

    /**
     * @param Context                  $context
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param TransportBuilder         $transportBuilder
     * @param PostNLHelper             $data
     * @param Webshop                  $webshop
     * @param Log                      $logging
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransportBuilder $transportBuilder,
        PostNLHelper $data,
        Webshop $webshop,
        Log $logging
    ) {
        $this->transportBuilder     = $transportBuilder;
        $this->postNLHelperData     = $data;

        parent::__construct(
            $context,
            $shipmentRepository,
            $postNLShipmentRepository,
            $searchCriteriaBuilder,
            $webshop,
            $logging
        );
    }

    /**
     * @return void
     * @throws MailException
     */
    public function send()
    {
        try {
            $this->trackAndTraceEmail->sendMessage();
        } catch (MailException $exception) {
            $this->logging->addCritical($exception->getLogMessage());
        }
    }

    /**
     * @param Shipment $shipment
     * @param string $url
     *
     * @return $this
     */
    public function set($shipment, $url)
    {
        $template  = $this->webshopConfig->getTrackAndTraceEmailTemplate($shipment->getStoreId());
        $transport = $this->transportBuilder->setTemplateIdentifier($template);
        $transport->setTemplateOptions([
            'area'  => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
        ]);
        $transport->setTemplateVars([
           'order_id'      => $shipment->getIncrementId(),
           'dateAndTime'   => $this->postNLHelperData->getDateYmd(),
           'url'           => $url
        ]);
        $transport->setFrom('general');
        $address = $shipment->getShippingAddress();
        $transport->addTo(
            $address->getEmail(),
            $address->getFirstname() . ' '. $address->getLastname()
        );
        $this->logging->addInfo('Track And Trace email build for :'. $address->getEmail());
        $this->trackAndTraceEmail = $transport->getTransport();
    }
}
