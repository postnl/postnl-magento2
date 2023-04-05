<?php

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
