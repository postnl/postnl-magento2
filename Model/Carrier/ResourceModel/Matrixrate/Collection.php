<?php

namespace TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init(
            \TIG\PostNL\Model\Carrier\Matrixrate::class,
            \TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate::class
        );
    }
}
