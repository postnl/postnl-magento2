<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use TIG\PostNL\Config\Source\Options\ProductOptions;

class ShipmentType implements OptionSourceInterface
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * ShipmentType constructor.
     *
     * @param ProductOptions $productOptions
     */
    public function __construct(
        ProductOptions $productOptions
    ) {
        $this->productOptions = $productOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->productOptions->toOptionArray();
    }
}
