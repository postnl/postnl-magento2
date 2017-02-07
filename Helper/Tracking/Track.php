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
use \Magento\Sales\Model\Order\Shipment\TrackFactory;
use \Magento\Shipping\Model\Tracking\Result\StatusFactory;
use \TIG\PostNL\Config\Provider\Webshop;
use \Magento\Sales\Model\Order\Shipment;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order\ShipmentRepository;
use \TIG\PostNL\Helper\Tracking\Mail;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

/**
 * Class Track
 *
 * @package TIG\PostNL\Helper\Tracking
 */
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
     */
    public function __construct(
        Context $context,
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StatusFactory $statusFactory,
        Mail $mail,
        Webshop $webshop
    ) {
        $this->trackFactory             = $trackFactory;
        $this->trackStatusFactory       = $statusFactory;
        $this->trackAndTraceEmail       = $mail;

        parent::__construct(
            $context,
            $shipmentRepository,
            $postNLShipmentRepository,
            $searchCriteriaBuilder,
            $webshop
        );
    }

    /**
     * @param Shipment $shipment
     *
     * @return $this
     */
    public function set($shipment)
    {
        $trackingNumbers = [];
        foreach ($this->getPostNLshipments($shipment->getId()) as $postnlShipment) {
            $trackingNumbers[] = $postnlShipment->getMainBarcode();
            //@codingStandardsIgnoreStart
            if (!$this->webshopConfig->isTrackAndTraceEnabled()) {
                continue;
            }
            //@codingStandardsIgnoreEnd
            $this->trackAndTraceEmail->set(
                $shipment,
                $this->getTrackAndTraceUrl($postnlShipment->getMainBarcode())
            );
            $this->trackAndTraceEmail->send();
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
     * @param $trackingNumbers
     */
    private function addTrackingNumbersToShipment($shipment, $trackingNumbers)
    {
        foreach ($trackingNumbers as $number) {
            $track = $this->trackFactory->create();
            $track->setNumber($number);
            $track->setCarrierCode('tig_postnl');
            $track->setTitle('PostNL');
            $shipment->addTrack($track);
        }

        /**
         * @notice: Magento Bug, can not save track after shipment creation (addTrack is triggert on _afterSave)
         *        So when the shipment is saved the packages value is automaticly s6:"a:{}" which will return in
         *        a fatal error in shipment view.
         * @codingStandardsIgnoreLine
         * @todo : Recalculate packages and set correct data.
         */
        $shipment->setPackages([]);
        $this->shimpentRepository->save($shipment);
    }
}
