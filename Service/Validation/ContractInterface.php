<?php

namespace TIG\PostNL\Service\Validation;

interface ContractInterface
{
    /**
     * Validate the data. Returns false when the validation fails.
     *
     * @param $line
     *
     * @return bool|mixed
     */
    public function validate($line);
}
