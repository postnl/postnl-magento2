<?php

namespace TIG\PostNL\Block\Adminhtml\Matrix;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Layout\Generic;

class Back extends Generic implements ButtonProviderInterface
{
    /** @var UrlInterface  */
    private $urlBuilder;

    /**
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $data
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
            'on_click' => "setLocation('" . $this->urlBuilder->getUrl('*/*/') . "' )",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }
}
