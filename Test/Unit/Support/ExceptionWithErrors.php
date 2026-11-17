<?php

namespace TIG\PostNL\Test\Unit\Support;

class ExceptionWithErrors extends \Exception
{
    public function getErrors()
    {
    }
}
