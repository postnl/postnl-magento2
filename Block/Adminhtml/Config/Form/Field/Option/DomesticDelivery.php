<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Form\Field\Option;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use TIG\PostNL\Config\Source\Options\DefaultOptions;
use TIG\PostNL\Config\Source\Options\ProductOptions;

class DomesticDelivery extends Select
{
    /**
     * @var DefaultOptions
     */
    private $defaultOptions;
    private ProductOptions $productOptions;

    public function __construct(
        Context $context,
        DefaultOptions $defaultOptions,
        ProductOptions $productOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->defaultOptions = $defaultOptions;
        $this->productOptions = $productOptions;
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $method = $this->_data['method'] ?? 'getAlternativeDeliveryOptions';
            if (method_exists($this->defaultOptions, $method)) {
                $this->setOptions($this->defaultOptions->$method());
            } else {
                $this->setOptions($this->productOptions->$method());
            }
        }

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }
}
