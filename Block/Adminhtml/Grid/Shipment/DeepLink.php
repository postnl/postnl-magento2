<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Renderer\DeepLink as Renderer;

class DeepLink extends AbstractGrid
{
    /**
     * @var Renderer
     */
    private $deepLinkRenderer;

    /**
     * DeepLink constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Renderer           $deepLinkRenderer
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Renderer $deepLinkRenderer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->deepLinkRenderer = $deepLinkRenderer;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        if (!isset($item['entity_id'])) {
            return '';
        }

        return $this->deepLinkRenderer->render($item['entity_id']);
    }
}
