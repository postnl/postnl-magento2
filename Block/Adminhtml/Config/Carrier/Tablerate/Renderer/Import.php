<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class Import extends Template implements RendererInterface
{
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::config/carrier/import.phtml';

    /** @var mixed */
    private $timeConditionName;

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * @param $timeConditionName
     */
    public function updateTimeConditionName($timeConditionName)
    {
        $this->timeConditionName = $timeConditionName;
    }

    /**
     * @return mixed
     */
    public function getTimeConditionName()
    {
        return $this->timeConditionName;
    }
}
