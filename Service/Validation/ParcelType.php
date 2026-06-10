<?php

namespace TIG\PostNL\Service\Validation;

use function strtolower;

class ParcelType implements ContractInterface
{
    /**
     * Validate the data. Returns false when the
     *
     * @param $line
     *
     * @return bool|mixed
     */
    public function validate($line)
    {
        $line = strtolower((string) $line);

        return match ($line) {
            '', '0', '*' => '*',
            'pakket', 'regular' => 'regular',
            'extra@home', 'extra @ home', 'extra_@_home', 'extraathome', 'extra_at_home' => 'extra@home',
            'pakjegemak', 'pakje_gemak', 'pakje gemak', 'postkantoor', 'post office' => 'pakjegemak',
            'letterbox_package' => 'letterbox_package',
            'boxable_packets' => 'boxable_packets',
            default => false,
        };

    }
}
