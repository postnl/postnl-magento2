<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

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
        $line = strtolower($line);
        switch ($line) {
            case '':
            case '0':
            case '*':
                return '*';

            case 'regular':
                return 'regular';

            case 'extra@home':
            case 'extra @ home':
            case 'extra_@_home':
            case 'extraathome':
            case 'extra_at_home':
                return 'extra@home';
        }

        return false;
    }
}
