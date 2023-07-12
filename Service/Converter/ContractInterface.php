<?php

namespace TIG\PostNL\Service\Converter;

interface ContractInterface
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
    public function convert($value);
}
