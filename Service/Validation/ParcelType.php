<?php

namespace TIG\PostNL\Service\Validation;

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
        $line = strtolower((string)$line);
        switch ($line) {
            case '':
            case '0':
            case '*':
                return '*';

            case 'pakket':
            case 'regular':
                return 'regular';

            case 'extra@home':
            case 'extra @ home':
            case 'extra_@_home':
            case 'extraathome':
            case 'extra_at_home':
                return 'extra@home';

            case 'pakjegemak':
            case 'pakje_gemak':
            case 'pakje gemak':
            case 'PakjeGemak':
            case 'postkantoor':
            case 'post office':
                return 'pakjegemak';

            case 'letterbox_package':
                return 'letterbox_package';

            case 'boxable_packets':
                return 'boxable_packets';
        }

        return false;
    }
}
