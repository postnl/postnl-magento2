<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use TIG\PostNL\Block\Adminhtml\Renderer\SmartReturnEmail as Renderer;

class SmartReturnEmail extends AbstractGrid
{
    /**
     * @var Renderer
     */
    private $smartReturnEmail;

    /**
     * SmartReturnEmail constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Renderer           $smartReturnEmail
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Renderer $smartReturnEmail,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->smartReturnEmail = $smartReturnEmail;
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

        return $this->smartReturnEmail->render($item['entity_id']);
    }
}
