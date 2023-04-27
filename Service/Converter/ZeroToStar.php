<?php

namespace TIG\PostNL\Service\Converter;

class ZeroToStar implements ContractInterface
{
    /**
     * Convert the value.
     *
     * @param $value
     *
     * @return mixed
     *
     * @throws \TIG\PostNL\Exception
     */
    public function convert($value)
    {
        if ($value == '0') {
            return '*';
        }

        return $value;
    }
}
