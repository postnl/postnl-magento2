<?php

namespace TIG\PostNL\Config\Source\Matrixrate;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory;

class Websites implements ArrayInterface
{
    /** @var mixed */
    protected $_options;

    /** @var CollectionFactory  */
    private $_websiteCollectionFactory;

    /**
     * @param CollectionFactory $websiteCollectionFactory
     */
    public function __construct(
        CollectionFactory $websiteCollectionFactory
    ) {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * Retrieve websites collection of system
     *
     * @return CollectionFactory Collection
     */
    public function getWebsiteLists()
    {
        return $this->_websiteCollectionFactory->create();
    }

    /**
     * @param bool $isMultiselect
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->_options) {
            $this->_options = $this->getWebsiteLists()->toOptionArray(false);
        }

        $options = $this->_options;

        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please select--')]);
        }

        return $options;
    }
}
