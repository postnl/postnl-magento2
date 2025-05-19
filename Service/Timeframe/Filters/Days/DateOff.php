<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;
use TIG\PostNL\Service\Timeframe\Filters\DaysSkipInterface;

class DateOff implements DaysFilterInterface, DaysSkipInterface
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

    public function skip(\DateTimeInterface $day): bool
    {
        $updated = false;
        $dates = $this->shippingOptions->getDeliveryOff();
        if ($dates) {
            $interval = new \DateInterval('P1D');
            $countDates = count($dates);
            $currentDate = $day->format('d-m-Y');
            for ($i = 0; $i < $countDates; $i++) {
                if ($dates[$i] === $currentDate) {
                    $day->add($interval);
                    // Start walk from the start, in case any dates are saved first
                    $i = -1;
                    $currentDate = $day->format('d-m-Y');
                    $updated = true;
                }
            }
        }
        return $updated;
    }
}
