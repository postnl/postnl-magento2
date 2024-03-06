<?php

namespace TIG\PostNL\Config\Source\General;

use Magento\Framework\Data\OptionSourceInterface;

class Logging implements OptionSourceInterface
{

    private $levels = [
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

    /**
     * Return logging option array
     * @return array
     */
    public function toOptionArray()
    {

        $options = [];

        foreach ($this->levels as $key => $value) {
            $options[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $options;
    }
}
