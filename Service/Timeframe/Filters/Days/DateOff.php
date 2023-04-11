<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Timeframe\Filters\Days;

use Magento\Framework\Serialize\SerializerInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Timeframe\Filters\DaysFilterInterface;

/**
 * Class DateOff
 * This will cut off days which are present in Delivery Date Off.
 */
class DateOff implements DaysFilterInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ShippingOptions $shippingOptions
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        SerializerInterface $serializer
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->serializer = $serializer;
    }

    /**
     * @param $days
     * @return array
     */
    public function filter($days): array
    {
        $dates = $this->shippingOptions->getDeliveryDateOff();
        if ($dates) {
            $days = array_filter($days, function ($day) use($dates) {
                return !in_array($day->Date, $dates);
            });
        }

        return array_values($days);
    }
}
