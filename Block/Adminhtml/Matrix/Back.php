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
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Layout\Generic;

class Back extends Generic implements ButtonProviderInterface
{
    /** @var Action  */
    private $action;

    /**
     * @param UiComponentFactory    $uiComponentFactory
     * @param Action                $action
     * @param array                 $data
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        Action $action,
        $data = []
    ) {
        $this->action = $action;
        parent::__construct($uiComponentFactory, $data);
    }

    /**
     * Create Button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => "setLocation('" . $this->action->getUrl('*/*/') . "' )",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }
}
