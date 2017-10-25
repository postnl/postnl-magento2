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

use TIG\PostNL\Exception as PostNLException;

class Factory
{
    /**
     * @var ContractInterface[]
     */
    private $validators;

    /**
     * Factory constructor.
     *
     * @param ContractInterface[] $validators
     */
    public function __construct(
        $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @param $validator
     *
     * @throws PostNLException
     */
    private function checkImplementation($validator)
    {
        $implementations = class_implements($validator);

        if (!array_key_exists(ContractInterface::class, $implementations)) {
            throw new PostNLException(__('Class is not an implementation of %1', ContractInterface::class));
        }
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|mixed
     * @throws PostNLException
     */
    public function validate($type, $value)
    {
        foreach ($this->validators as $validator) {
            $this->checkImplementation($validator);
        }

        $result = $this->callValidator($type, $value);
        if ($result !== null) {
            return $result;
        }

        throw new PostNLException(__('There is no implementation found for the "%1" validator', $type));
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool
     */
    private function callValidator($type, $value)
    {
        switch ($type) {
            case 'price':
            case 'weight':
            case 'subtotal':
            case 'quantity':
                return $this->validators['decimal']->validate($value);

            case 'parcel-type':
                return $this->validators['parcelType']->validate($value);

            case 'country':
                return $this->validators['country']->validate($value);

            case 'region':
                return $this->validators['region']->validate($value);

            case 'duplicate-import':
                return $this->validators['duplicateImport']->validate($value);
        }
    }

    /**
     * Check if the reset method is available and if so calls them.
     */
    public function resetData()
    {
        foreach ($this->validators as $validator) {
            $this->callResetData($validator);
        }
    }

    /**
     * @param $validator
     */
    private function callResetData($validator)
    {
        if (method_exists($validator, 'resetData')) {
            $validator->resetData();
        }
    }
}
