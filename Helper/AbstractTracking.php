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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Logging\Log;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AbstractExtensibleObject;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;

abstract class AbstractTracking extends AbstractHelper
{
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
     * @var ShipmentBarcodeRepositoryInterface
     */
    //@codingStandardsIgnoreLine
    protected $shipmentBarcodeRepositoryInterface;
    /**
     * @var ReturnOptions
     */
    private $returnOptions;

    /**
     * @param Context                            $context
     * @param PostNLShipmentRepository           $postNLShipmentRepository
     * @param SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param Webshop                            $webshop
     * @param Log                                $logging
     * @param ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepositoryInterface
     * @param ScopeConfigInterface               $scopeConfig
     * @param ReturnOptions                      $returnOptions
     */
    public function __construct(
        Context $context,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Webshop $webshop,
        Log $logging,
        ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepositoryInterface,
        ScopeConfigInterface $scopeConfig,
        ReturnOptions $returnOptions
    ) {
        $this->postNLShipmentRepository           = $postNLShipmentRepository;
        $this->searchCriteriaBuilder              = $searchCriteriaBuilder;
        $this->webshopConfig                      = $webshop;
        $this->logging                            = $logging;
        $this->shipmentBarcodeRepositoryInterface = $shipmentBarcodeRepositoryInterface;
        $this->scopeConfig                        = $scopeConfig;
        $this->returnOptions                      = $returnOptions;
        parent::__construct($context);
    }

    /**
     * @param $shipmentId
     *
     * @return AbstractExtensibleObject[]
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
     * @return AbstractExtensibleObject|\Magento\Framework\Api\SearchResults|\TIG\PostNL\Api\Data\ShipmentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLshipmentByTracking($trackingNumber)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('main_barcode', $trackingNumber);
        $searchCriteria->setPageSize(1);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());

        if (!$list->getItems()) {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('value', $trackingNumber);
            $searchCriteria->setPageSize(1);
            $shipmentBarcode = $this->shipmentBarcodeRepositoryInterface->getList($searchCriteria->create());
            $shipmentId = $shipmentBarcode->getItems()[0]->getParentId();
            $list = $this->postNLShipmentRepository->getById($shipmentId);

            return $list;
        }

        return $list->getItems()[0];
    }

    /**
     * @param        $trackingNumber
     * @param string $type
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    //@codingStandardsIgnoreLine
    protected function getTrackAndTraceUrl($trackingNumber, $type = 'C')
    {
        $isReturn = false;
        $returnCountry = 'NL';
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLshipmentByTracking($trackingNumber);
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
        $address = $postNLShipment->getShippingAddress();

        if ($postNLShipment->getReturnBarcode() === $trackingNumber) {
            $isReturn = true;
        }

        if ($isReturn && $this->returnOptions->isReturnActive()) {
            $returnCountry = 'BE';
        }

        return $this->generateTrackAndTraceUrl($address, $trackingNumber, $type, $isReturn, $returnCountry);
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
    protected function generateTrackAndTraceUrl(OrderAddressInterface $address, $trackingNumber, $type ,$isReturn, $returnCountry)
    {
        $order = $address->getOrder();
        $store = $order->getStore();
        $storeLocale = $this->scopeConfig->getValue(
            'general/locale/code',
            ScopeInterface::SCOPE_STORE,
            $store->getStoreId()
        );

        $language = substr($storeLocale, 3, 2);
        $params = [
            'B' => $trackingNumber,
            'D' => $address->getCountryId(),
            'P' => str_replace(' ', '', $address->getPostcode() ?? ''),
            'T' => $type,
            'L' => $language
        ];

        if ($isReturn === true) {
            $params['D'] = $returnCountry;
            $params['P'] = $this->returnOptions->getZipcode();
        }

        return $this->webshopConfig->getTrackAndTraceServiceUrl() . http_build_query($params);
    }
}
