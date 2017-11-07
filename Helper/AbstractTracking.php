<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper;

use Magento\Sales\Api\Data\OrderAddressInterface;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Logging\Log;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Framework\Api\AbstractExtensibleObject;

abstract class AbstractTracking extends AbstractHelper
{
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
     * @var Webshop
     */
    //@codingStandardsIgnoreLine
    protected $webshopConfig;

    /**
     * @var Log
     */
    //@codingStandardsIgnoreLine
    protected $logging;

    /**
     * @param Context                  $context
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param Webshop                  $webshop
     * @param Log                      $logging
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Webshop $webshop,
        Log $logging
    ) {
        $this->shimpentRepository       = $shipmentRepository;
        $this->postNLShipmentRepository = $postNLShipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->webshopConfig            = $webshop;
        $this->logging                  = $logging;
        parent::__construct($context);
    }

    /**
     * @param $shipmentId
     *
     * @return PostNLShipment[]
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
     * @return AbstractExtensibleObject
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
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLshipmentByTracking($trackingNumber);
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shimpentRepository->get($postNLShipment->getShipmentId());
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
        $address = $shipment->getShippingAddress();

        return $this->generateTrackAndTraceUrl($address, $trackingNumber, $type);
    }

    /**
     * Generate the Track&Trace url based on an address.
     *
     * @param OrderAddressInterface $address
     * @param                       $trackingNumber
     * @param                       $type
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function generateTrackAndTraceUrl(OrderAddressInterface $address, $trackingNumber, $type)
    {
        $params = [
            'B=' . $trackingNumber,
            'D=' . $address->getCountryId(),
            'P=' . $address->getPostcode(),
            'T=' . $type,
        ];

        return $this->webshopConfig->getTrackAndTraceServiceUrl() . implode('&', $params);
    }
}
