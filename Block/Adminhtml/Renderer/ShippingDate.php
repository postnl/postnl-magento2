<?php

namespace TIG\PostNL\Block\Adminhtml\Renderer;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use TIG\PostNL\Model\Shipment;

class ShippingDate
{
    /**
     * @var TimezoneInterface
     */
    private $todayDate;

    /**
     * @var TimezoneInterface
     */
    private $shipAtDate;

    /**
     * @param TimezoneInterface          $todayDate
     * @param TimezoneInterface          $shipAtDate
     */
    public function __construct(
        TimezoneInterface $todayDate,
        TimezoneInterface $shipAtDate
    ) {
        $this->todayDate = $todayDate;
        $this->shipAtDate = $shipAtDate;
    }

    /**
     * @param null|Shipment $item
     *
     * @return string|null
     */
    public function render($item)
    {
        $shipAt = $this->getShipAt($item);
        if ($shipAt === null) {
            return 'At the first opportunity';
        }

        return $this->formatShippingDate($shipAt);
    }

    /**
     * @param null|Shipment $shipAt
     *
     * @return null|string
     */
    private function getShipAt($shipAt)
    {
        if ($shipAt instanceof Shipment) {
            $shipAt = $shipAt->getShipAt();
        }

        return $shipAt;
    }

    /**
     * @param $shipAt
     *
     * @return null|int
     */
    private function formatShippingDate($shipAt)
    {
        $now = $this->todayDate->date('today', null, false, false);
        $whenToShip = $this->shipAtDate->date(strtotime($shipAt), null, false);
        $difference = $now->diff($whenToShip);
        $days = $difference->days;

        if ($days == 0) {
            return __('Today');
        }

        if (!$difference->invert && $days === 1) {
            return __('Tomorrow');
        }

        if (!$difference->invert) {
            return __('In %1 days', [$days]);
        }

        return $whenToShip->format('d M. Y');
    }
}
