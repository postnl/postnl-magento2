<?php

namespace TIG\PostNL\Block\Adminhtml\Renderer;

use TIG\PostNL\Config\Source\Options\ProductOptions;

class ShipmentType
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
     * @param $code
     * @param $type
     *
     * @return string
     */
    public function render($code, $type)
    {
        $type   = $this->productOptions->getLabel($code, $type);
        $output = (string)$type['label'];

        if ($type['type']) {
            $output .= ' <em>' . $type['type'] . '</em>';
        }

        $comment = $type['comment'];
        if (!$comment) {
            return $output;
        }

        $output .= '<br><em style="font-size:9px;" title="'.$type['comment'].'">' . $comment . '</em>';

        return $output;
    }
}
