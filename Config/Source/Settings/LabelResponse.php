<?php

namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelResponse implements OptionSourceInterface
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
            ['value' => static::DOWNLOAD_RESPONSE, 'label' => __('Download labels directly')],
            ['value' => static::INBROWSER_RESPONSE, 'label' => __('Open labels in new browser window')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
