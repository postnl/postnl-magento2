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
namespace TIG\PostNL\Helper\Tracking;

use Magento\Framework\App\Config\ScopeConfigInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Helper\AbstractTracking;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Shipping\Model\Tracking\Result\StatusFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;
use TIG\PostNL\Service\Handler\BarcodeHandler;

// @codingStandardsIgnoreFile
class Track extends AbstractTracking
{
    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var StatusFactory
     */
    private $trackStatusFactory;

    /**
     * @var Mail
     */
    private $trackAndTraceEmail;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /** @var BarcodeHandler */
    private $barcodeHandler;

    /**
     * @param Context                            $context
     * @param ShipmentRepository                 $shipmentRepository
     * @param TrackFactory                       $trackFactory
     * @param PostNLShipmentRepository           $postNLShipmentRepository
     * @param SearchCriteriaBuilder              $searchCriteriaBuilder
     * @param StatusFactory                      $statusFactory
     * @param Mail                               $mail
     * @param Webshop                            $webshop
     * @param Log                                $logging
     * @param ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepositoryInterface
     * @param BarcodeHandler                     $barcodeHandler
     * @param ScopeConfigInterface               $scopeConfig
     * @param ReturnOptions                      $returnOptions
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        TrackFactory $trackFactory,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StatusFactory $statusFactory,
        Mail $mail,
        Webshop $webshop,
        Log $logging,
        ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepositoryInterface,
        BarcodeHandler $barcodeHandler,
        ScopeConfigInterface $scopeConfig,
        ReturnOptions $returnOptions
    ) {
        $this->trackFactory             = $trackFactory;
        $this->trackStatusFactory       = $statusFactory;
        $this->trackAndTraceEmail       = $mail;
        $this->shipmentRepository       = $shipmentRepository;
        $this->barcodeHandler           = $barcodeHandler;

        parent::__construct(
            $context,
            $postNLShipmentRepository,
            $searchCriteriaBuilder,
            $webshop,
            $logging,
            $shipmentBarcodeRepositoryInterface,
            $scopeConfig,
            $returnOptions
        );
    }

    /**
     * @param Shipment|ShipmentInterface $shipment
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function set($shipment)
    {
        $trackingNumbers = [];
        $postnlShipments = $this->getPostNLshipments($shipment->getId());

        foreach ($postnlShipments as $postnlShipment) {
            $trackingNumbers[] = $postnlShipment->getMainBarcode();
        }

        $this->addTrackingNumbersToShipment($shipment, $trackingNumbers);

        $postNLShipment = $this->getPostNLShipment($shipment->getId());
        $shippingAddress = $shipment->getShippingAddress();

        if ($this->barcodeHandler->canAddReturnBarcodes($shippingAddress->getCountryId(), $postNLShipment)) {
            $this->addReturnTrackingNumbersToShipment($postNLShipment);
        }

    }

    /**
     * @param $tracking
     *
     * @return \Magento\Shipping\Model\Tracking\Result
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($tracking)
    {
        $trackingStatus = $this->trackStatusFactory->create();
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setCarrier('tig_postnl');
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setCarrierTitle('PostNL');
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setTracking($tracking);
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setUrl($this->getTrackAndTraceUrl($tracking));

        return $trackingStatus;
    }

    /**
     * @param Shipment $shipment
     */
    public function send($shipment)
    {
        $postnlShipments = $this->getPostNLshipments($shipment->getId());

        foreach ($postnlShipments as $postnlShipment) {
            $this->sendTrackAndTraceEmail($shipment, $postnlShipment);
        }
    }

    /**
     * @notice: Magento Bug, can not save track after shipment creation (addTrack is triggert on _afterSave)
     *        So when the shipment is saved the packages value is automaticly s6:"a:{}" which will return in
     *        a fatal error in shipment view.
     *
     * @param Shipment $shipment
     * @param $trackingNumbers
     */
    private function addTrackingNumbersToShipment($shipment, $trackingNumbers)
    {
        $this->logging->debug('Adding trackingnumbers to shipment_id : '. $shipment->getId(), $trackingNumbers);

        $shipment = $this->resetTrackingKey($shipment);
        foreach ($trackingNumbers as $number) {
            $track = $this->trackFactory->create();
            $track->setNumber($number);
            $track->setCarrierCode('tig_postnl');
            $track->setTitle('PostNL');
            $shipment->addTrack($track);
        }

        /**
         * @codingStandardsIgnoreLine
         * @todo : Recalculate packages and set correct data.
         */
        $shipment->setPackages([]);
        $this->shipmentRepository->save($shipment);
    }

    /**
     * @param $postNLShipment
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function addReturnTrackingNumbersToShipment($postNLShipment)
    {
        $shipment = $postNLShipment->getShipment();
        $this->logging->debug('Adding return trackingnumbers to shipment_id : '. $shipment->getId());
        $returnItems = $this->getList($postNLShipment);

        foreach ($returnItems as $item) {
            $track = $this->trackFactory->create();
            $track->setNumber($item->getValue());
            $track->setCarrierCode('tig_postnl');
            $track->setTitle(__('PostNL Return'));
            /** @noinspection PhpUndefinedMethodInspection */
            $shipment->addTrack($track);
        }

        /**
         * @codingStandardsIgnoreLine
         * @todo : Recalculate packages and set correct data.
         */
        $shipment->setPackages([]);
        $this->shipmentRepository->save($shipment);
    }

    /**
     * @notice : Because the tracks key is cached within the data object it could be this will result in an empty array.
     *           When this happends core Magento will trow an Exception because its will try
     *           to add an item on an non-object.
     *
     * @param Shipment $shipment
     * @return Shipment $shipment
     */
    private function resetTrackingKey($shipment)
    {
        $data = $shipment->getData();
        if (isset($data['tracks']) && empty($data['tracks'])) {
            unset($data['tracks']);
        }

        $shipment->setData($data);
        return $shipment;
    }

    /**
     * @param Shipment $shipment
     * @param PostNLShipment $postnlShipment
     */
    private function sendTrackAndTraceEmail($shipment, $postnlShipment)
    {
        if (!$this->webshopConfig->isTrackAndTraceEnabled()) {
            return;
        }

        $this->trackAndTraceEmail->set(
            $shipment,
            $this->getTrackAndTraceUrl($postnlShipment->getMainBarcode())
        );
        $this->trackAndTraceEmail->send();
    }

    /**
     * @param $postNLShipment
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    private function getList($postNLShipment)
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $postNLShipment->getId());
        $this->searchCriteriaBuilder->addFilter('type', 'return');
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $result = $this->shipmentBarcodeRepositoryInterface->getList($searchCriteria);
        $list = $result->getItems();

        return $list;
    }

    /**
     * @param $shipmentId
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface|null
     */
    private function getPostNLShipment($shipmentId)
    {
        return $this->postNLShipmentRepository->getByShipmentId($shipmentId);
    }
}
