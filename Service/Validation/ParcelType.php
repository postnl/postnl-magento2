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

        if ($this->isWildcard($line)) {
            return '*';
        }

        if ($this->isRegular($line)) {
            return 'regular';
        }

        if ($this->isExtraAtHome($line)) {
            return 'extra@home';
        }

        if ($this->isPakjegemak($line)) {
            return 'pakjegemak';
        }

        return false;
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isWildcard($line)
    {
        return in_array($line, ['', '0', '*']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isRegular($line)
    {
        return in_array($line, ['pakket', 'regular']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isExtraAtHome($line)
    {
        return in_array($line, ['extra@home', 'extra @ home', 'extra_@_home', 'extraathome', 'extra_at_home']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isPakjegemak($line)
    {
        $options = ['pakjegemak', 'pakje_gemak', 'pakje gemak', 'PakjeGemak', 'postkantoor', 'post office'];

        return in_array($line, $options);
    }
}
