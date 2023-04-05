<?php

namespace TIG\PostNL\Service\Shipment;

class GuaranteedOptions
{
    const GUARANTEED_TYPE_CARGO   = 'cargo';
    const GUARANTEED_TYPE_PACKAGE = 'package';

    private $availableProductOptions = [
        '1000' => [
            'Characteristic' => '118',
            'Option'         => '007',
        ],
        '1200' => [
            'Characteristic' => '118',
            'Option'         => '008',
        ],
        '1400' => [
            'Characteristic' => '118',
            'Option'         => '013',
        ],
        '1700' => [
            'Characteristic' => '118',
            'Option'         => '012',
        ],
        'none' => [
            'Characteristic' => '000',
            'Option'         => '000',
        ]
    ];

    /**
     * @param      $time
     * @param bool $flat
     *
     * @return array|null
     */
    public function get($time, $flat = false)
    {
        if (!array_key_exists($time, $this->availableProductOptions)) {
            return null;
        }

        if ($flat) {
            return $this->availableProductOptions[$time];
        }

        return ['ProductOption' => $this->availableProductOptions[$time]];
    }
}
