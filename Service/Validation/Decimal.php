<?php

namespace TIG\PostNL\Service\Validation;

class Decimal implements ContractInterface
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
        if (!is_numeric($line)) {
            return false;
        }

        $line = (float)sprintf('%.4F', $line);
        if ($line < 0.0000) {
            return false;
        }

        return $line;
    }
}
