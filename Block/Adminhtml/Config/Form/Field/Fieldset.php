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
