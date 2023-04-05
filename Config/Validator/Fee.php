<?php

namespace TIG\PostNL\Config\Validator;

use Magento\Framework\App\Config\Value;

class Fee extends Value
{
    /**
     * Make sure the fee is entered with an . instead of an ,.
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = trim($value);
        $value = str_replace(',', '.', $value);
        $this->setValue($value);

        return parent::beforeSave();
    }
}
