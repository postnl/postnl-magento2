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
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatterInterface;

    /**
     * @param TimezoneInterface          $todayDate
     * @param TimezoneInterface          $shipAtDate
     * @param DateTimeFormatterInterface $dateTimeFormatterInterface
     */
    public function __construct(
        TimezoneInterface $todayDate,
        TimezoneInterface $shipAtDate,
        DateTimeFormatterInterface $dateTimeFormatterInterface
    ) {
        $this->dateTimeFormatterInterface = $dateTimeFormatterInterface;
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
            return null;
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
        $now = $this->todayDate->date(null, null, false);
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
