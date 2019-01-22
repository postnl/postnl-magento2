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
namespace TIG\PostNL\Plugin\Admin\Ui\Component;

use Magento\Ui\Component\MassAction as UiComponentMassAction;
use TIG\PostNL\Config\Provider\AccountConfiguration;

class MassAction
{
    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @param AccountConfiguration $accountConfiguration
     */
    public function __construct(
        AccountConfiguration $accountConfiguration
    ) {
        $this->accountConfiguration = $accountConfiguration;
    }

    /**
     * When the PostNL extension is disabled we want to remove all PostNL massactions, which are always
     * prefixed by postnl.
     *
     * @param UiComponentMassAction $massAction
     */
    public function afterPrepare(UiComponentMassAction $massAction)
    {
        $config = $massAction->getData('config');
        if (!$this->accountConfiguration->isModusOff() || !isset($config['actions'])) {
            return;
        }

        $config['actions'] = array_filter($config['actions'], function ($action) {
            $typeStart = substr($action['type'], 0, 6);

            return strtolower($typeStart) != 'postnl';
        });

        $config['actions'] = array_values($config['actions']);

        $massAction->setData('config', $config);
    }
}
