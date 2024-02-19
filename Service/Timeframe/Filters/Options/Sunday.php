<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Options;

use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;

class Sunday implements OptionsFilterInterface
{
    const TIMEFRAME_OPTION_SUNDAY = 'Sunday';

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
     * @param array|object $options
     *
     * @return array|object
     */
    public function filter($options)
    {
        $filterdOptions = array_filter($options, [$this, 'canDeliver']);
        return array_values($filterdOptions);
    }

    /**
     * @param $option
     *
     * @return bool
     */
    public function canDeliver($option)
    {
        $option = $option->Options;
        if (!isset($option->string[0])) {
            return false;
        }

        $result = false;

        foreach ($option->string as $string) {
            if ($string !== static::TIMEFRAME_OPTION_SUNDAY) {
                $result = true;
            }

            if ($string === static::TIMEFRAME_OPTION_SUNDAY
                && $this->shippingOptions->isSundayDeliveryActive()) {
                $option->validatedType = $string;
                $result = true;
            }
        }

        return $result;
    }
}
