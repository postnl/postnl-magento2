<?php

namespace TIG\PostNL\Config\Source\Matrixrate;

use Magento\Framework\Option\ArrayInterface;

use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class Websites implements ArrayInterface
{

    protected $_options;

    public function __construct(

        CollectionFactory $websiteCollectionFactory
    )
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;

    }

    /**
     * Retrieve websites collection of system
     *
     * @return Website Collection
     */
    public function getWebsiteLists()
    {
        $collection = $this->_websiteCollectionFactory->create();
        return $collection;
    }

    public function toOptionArray($isMultiselect = false, $foregroundWebsites = '')
    {
        if (!$this->_options) {
            $this->_options = $this->getWebsiteLists()->toOptionArray(
                false
            );
        }

        $options = $this->_options;

        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
            array_unshift($options, ['value' => '*', 'label' => __('All websites')]);
        }

        return $options;
    }
}
