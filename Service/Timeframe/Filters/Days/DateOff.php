<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;

class DateOff implements DaysFilterInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * Filter delivery days and remove dates disabled in magento config.
     *
     * @param object|array $days
     * @return array
     */
    public function filter($days): array
    {
        $dates = $this->shippingOptions->getDeliveryOff();
        if ($dates) {
            $days = array_filter($days, function ($day) use ($dates) {
                return !in_array($day->Date, $dates);
            });
        }

        return array_values($days);
    }
}
