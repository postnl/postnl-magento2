<?php

namespace TIG\PostNL\Observer\TIGPostNLSetConfirmedAtBefore;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\Shipment;

class SendTrackAndTraceEmail implements ObserverInterface
{
    /** @var  Track */
    private $track;

    /**
     * @param Track $track
     */
    public function __construct(Track $track)
    {
        $this->track = $track;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getData('shipment');
        $magentoShipment = $shipment->getShipment();

        $this->track->send($magentoShipment);
    }
}
