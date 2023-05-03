<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid as MagentoGrid;
use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\CollectionFactory as MagentoCollectionFactory;

use TIG\PostNL\Model\Carrier\Tablerate;
use TIG\PostNL\Model\ResourceModel\Tablerate\CollectionFactory;

class Grid extends MagentoGrid
{
    /**
     * By overriding the parameters of the consturctor,
     * the PostNL Tablerate model and collection will be used instead those of OfflineShipping.
     *
     * @param Context                  $context
     * @param Data                     $backendHelper
     * @param MagentoCollectionFactory $collectionFactory
     * @param Tablerate                $tablerate
     * @param CollectionFactory        $postNLCollectionFactory
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        MagentoCollectionFactory $collectionFactory,
        Tablerate $tablerate,
        CollectionFactory $postNLCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $collectionFactory, $tablerate, $data);

        $this->_collectionFactory = $postNLCollectionFactory;
    }
}
