<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Helper\Data;

class DaysOff implements DaysFilterInterface
{
    private const XPATH_SHIPPING_OPTION_DELIVERY_DAYS_OFF = 'tig_postnl/delivery_days/delivery_days_off';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var Data
     */
    private $postNlHelper;

    /**
     * @param ShippingOptions $shippingOptions
     * @param Data $postNlHelper
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        Data $postNlHelper
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->postNlHelper = $postNlHelper;
    }

    /**
     * Filter delivery days and remove days of week disabled in magento config.
     *
     * @param object|array $days
     * @return array
     */
    public function filter($days): array
    {
        $daysOff = $this->shippingOptions->getDeliveryOff(self::XPATH_SHIPPING_OPTION_DELIVERY_DAYS_OFF);
        if ($daysOff) {
            $days = array_filter($days, function ($day) use ($daysOff) {
                $number = $this->postNlHelper->getDayOrWeekNumber($day->Date);
                return !in_array((string)$number, $daysOff);
            });
        }

        return array_values($days);
    }
}
