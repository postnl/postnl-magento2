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
     * @param Context                  $context
     * @param TrackFactory             $trackFactory
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param StatusFactory            $statusFactory
     * @param Mail                     $mail
     * @param Webshop                  $webshop
     * @param Log                      $logging
     */
    public function __construct(
        Context $context,
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StatusFactory $statusFactory,
        Mail $mail,
        Webshop $webshop,
        Log $logging
    ) {
        $this->trackFactory             = $trackFactory;
        $this->trackStatusFactory       = $statusFactory;
        $this->trackAndTraceEmail       = $mail;

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
     * @param Shipment|ShipmentInterface $shipment
     */
    public function set($shipment)
    {
        $trackingNumbers = [];
        $postnlShipments = $this->getPostNLshipments($shipment->getId());

        foreach ($postnlShipments as $postnlShipment) {
            $trackingNumbers[] = $postnlShipment->getMainBarcode();
        }

        $this->addTrackingNumbersToShipment($shipment, $trackingNumbers);
    }

    /**
     * @param $tracking
     *
     * @return \Magento\Shipping\Model\Tracking\Result
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
        $this->logging->addDebug('Adding trackingnumbers to shipment_id : '. $shipment->getId(), $trackingNumbers);

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
        $this->shimpentRepository->save($shipment);
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
        if (isset($data['tracks']) && count($data['tracks']) === 0) {
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
}
