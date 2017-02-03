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
use TIG\PostNL\Config\Provider\AddressConfiguration;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order\ShipmentRepository;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

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
     * @var AddressConfiguration
     */

    private $addressConfiguration;

    /**
     * @var \Magento\Framework\Mail\TransportInterface
     */
    private $trackAndTraceEmail;

    /**
     * @param Context                  $context
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param TransportBuilder         $transportBuilder
     * @param PostNLHelper             $data
     * @param AddressConfiguration     $addressConfiguration
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransportBuilder $transportBuilder,
        PostNLHelper $data,
        AddressConfiguration $addressConfiguration
    ) {
        $this->transportBuilder     = $transportBuilder;
        $this->postNLHelperData     = $data;
        $this->addressConfiguration = $addressConfiguration;

        parent::__construct(
            $context,
            $shipmentRepository,
            $postNLShipmentRepository,
            $searchCriteriaBuilder
        );
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send()
    {
        $this->trackAndTraceEmail->sendMessage();
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param string $url
     *
     * @return $this
     */
    public function set($shipment, $url)
    {
        $template  = $this->getTemplate($shipment->getStoreId());
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

        $this->trackAndTraceEmail = $transport->getTransport();
    }

    /**
     * @param $storeId
     *
     * @return mixed
     */
    private function getTemplate($storeId)
    {
        return $this->scopeConfig->getValue(
            'tig_postnl/track_and_trace_email/template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
