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

use TIG\PostNL\Service\Import\Exception as ImportException;

class DuplicateImport implements ContractInterface
{
    /**
     * @var array
     */
    private $hashes = [];

    /**
     * Validate the data. Returns false when the
     *
     * @param $line
     *
     * @return bool|mixed
     * @throws ImportException
     */
    public function validate($line)
    {
        if ($count = count($line) < 7) {
            // @codingStandardsIgnoreLine
            $message = __('The array to validate is expected to have 7 elements, you only have %1', $count);
            throw new ImportException($message);
        }

        return $this->validateHash($line);
    }

    /**
     * Delete all know hashes and start over.
     */
    public function resetData()
    {
        $this->hashes = [];
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function validateHash($line)
    {
        $hash = '%s-%s-%s-%F-%F-%d-%s';
        $hash = sprintf($hash, $line[0], $line[1], $line[2], $line[3], $line[4], $line[5], $line[6]);

        if (in_array($hash, $this->hashes)) {
            return false;
        }

        $this->hashes[] = $hash;

        return true;
    }
}
