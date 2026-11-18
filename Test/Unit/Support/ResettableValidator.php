<?php

namespace TIG\PostNL\Test\Unit\Support;

interface ResettableValidator extends \TIG\PostNL\Service\Validation\ContractInterface
{
    public function resetData();
}
