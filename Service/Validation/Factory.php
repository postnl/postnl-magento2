<?php

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
    public function validate($type, $value, $websiteId = null)
    {
        foreach ($this->validators as $validator) {
            $this->checkImplementation($validator);
        }

        $result = $this->callValidator($type, $value, $websiteId);

        if ($result !== null) {
            return $result;
        }

        throw new PostNLException(__('There is no implementation found for the "%1" validator', $type));
    }

    /**
     * @param $type
     * @param $value
     *
     * @return bool|null
     */
    private function callValidator($type, $value, $websiteId)
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
                $this->validators['country']->setWebsiteId($websiteId);
                return $this->validators['country']->validate($value);

            case 'region':
                return $this->validators['region']->validate($value);

            case 'duplicate-import':
                return $this->validators['duplicateImport']->validate($value);
        }
        return null;
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
