<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
