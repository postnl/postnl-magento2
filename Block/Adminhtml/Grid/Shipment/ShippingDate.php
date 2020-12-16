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
namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate as ShippingDateRenderer;

class ShippingDate extends AbstractGrid
{
    /**
     * @var ShippingDateRenderer
     */
    private $shippingDateRenderer;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param ContextInterface     $context
     * @param UiComponentFactory   $uiComponentFactory
     * @param ShippingDateRenderer $shippingDateRenderer
     * @param TimezoneInterface    $timezone
     * @param array                $components
     * @param array                $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShippingDateRenderer $shippingDateRenderer,
        TimezoneInterface $timezone,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->shippingDateRenderer = $shippingDateRenderer;
        $this->timezone = $timezone;
    }

    /**
     * @param $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        return $this->shippingDateRenderer->render($item['tig_postnl_ship_at']);
    }

    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');
        $config['filter'] = [
            'filterType' => 'dateRange',
            'templates' => [
                'date' => [
                    'options' => [
                        'dateFormat' => $config['dateFormat'] ?? $this->timezone->getDateFormatWithLongYear()
                    ]
                ]
            ]
        ];
        $this->setData('config', $config);
    }
}
