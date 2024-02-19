<?php

namespace TIG\PostNL\Config\Source\Settings;

use \Magento\Framework\Option\ArrayInterface;

class LabelResponse implements ArrayInterface
{
    const DOWNLOAD_RESPONSE  = 'attachment';
    const INBROWSER_RESPONSE = 'inline';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            ['value' => static::DOWNLOAD_RESPONSE, 'label' => __('Download pdf directly')],
            ['value' => static::INBROWSER_RESPONSE, 'label' => __('Open pdf in new browser window')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
