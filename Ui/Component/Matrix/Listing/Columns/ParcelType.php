<?php

namespace TIG\PostNL\Ui\Component\Matrix\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class ParcelType extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    // Give the correct translation to the correct field.
                    switch ($item[$fieldName]) {
                        case 'pakjkegemak':
                            $item[$fieldName] = __('Post office');
                            break;
                        case 'extra@home':
                            $item[$fieldName] = __('Extra@Home');
                            break;
                        case 'regular':
                            $item[$fieldName] = __('Regular');
                            break;
                        case 'letterbox_package':
                            $item[$fieldName] = __('(International) Letterbox Package');
                            break;
                        case 'boxable_packets':
                            $item[$fieldName] = __('Boxable Packet');
                            break;
                        case '*':
                            $item[$fieldName] = __('All parcel types');
                            break;
                    }
                }
            }
        }

        return $dataSource;
    }
}
