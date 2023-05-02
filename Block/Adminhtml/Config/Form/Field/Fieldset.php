<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Fieldset as MagentoFieldset;

class Fieldset extends MagentoFieldset
{
    private $classNames = [
        '1' => 'modus_live',
        '2' => 'modus_test',
        '0' => 'modus_off'
    ];

    /**
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreLine
    protected function _getFrontendClass($element)
    {
        $modus = $this->_scopeConfig->getValue(
            \TIG\PostNL\Config\Provider\AccountConfiguration::XPATH_GENERAL_STATUS_MODUS
        );

        $className = 'modus_off';
        if (array_key_exists($modus, $this->classNames)) {
            $className = $this->classNames[$modus];
        }

        return parent::_getFrontendClass($element) . ' ' . $className;
    }
}
