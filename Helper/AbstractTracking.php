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
namespace TIG\PostNL\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order\ShipmentRepository;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

/**
 * Class AbstractTracking
 *
 * @package TIG\PostNL\Helper
 */
abstract class AbstractTracking extends AbstractHelper
{
    const TRACK_AND_TRACE_SERVICE_URL = 'http://postnl.nl/tracktrace/?';

    /**
     * @var ShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $shimpentRepository;

    /**
     * @var PostNLShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $postNLShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    //@codingStandardsIgnoreLine
    protected $searchCriteriaBuilder;

    /**
     * @param Context                  $context
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shimpentRepository       = $shipmentRepository;
        $this->postNLShipmentRepository = $postNLShipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * @param $shipmentId
     *
     * @return \TIG\PostNL\Model\Shipment[]
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLshipments($shipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $shipmentId);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems();
    }

    /**
     * @param $trackingNumber
     *
     * @return \Magento\Framework\Api\AbstractExtensibleObject
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLshipmentByTracking($trackingNumber)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('main_barcode', $trackingNumber);
        $searchCriteria->setPageSize(1);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems()[0];
    }

    /**
     * @param $trackingNumber
     * @param string $type
     *
     * @return string
     */
    //@codingStandardsIgnoreLine
    protected function getTrackAndTraceUrl($trackingNumber, $type = 'C')
    {
        /** @var \TIG\PostNL\Model\Shipment $postNLShipment */
        $postNLShipment = $this->getPostNLshipmentByTracking($trackingNumber);
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shimpentRepository->get($postNLShipment->getShipmentId());
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
        $address  = $shipment->getShippingAddress();

        $lang = !in_array($address->getCountryId(), [
            'NL', 'DE', 'EN', 'FR', 'ED', 'IT', 'CN'
        ]) ? 'EN' : $address->getCountryId();

        $params = [
            'B='.$trackingNumber,
            '&D='.$lang,
            '&P='.$address->getPostcode(),
            '&T='.$type
        ];

        return self::TRACK_AND_TRACE_SERVICE_URL.implode('', $params);
    }
}
