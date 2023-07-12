<?php

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
