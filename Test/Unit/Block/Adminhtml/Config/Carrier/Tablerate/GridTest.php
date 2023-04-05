<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Config\Carrier\Tablerate;

use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Grid;
use TIG\PostNL\Model\ResourceModel\Tablerate\CollectionFactory;
use TIG\PostNL\Test\TestCase;

class GridTest extends TestCase
{
    protected $instanceClass = Grid::class;

    public function testInstance()
    {
        $instance = $this->getInstance();
        $collectionFactoryProperty = $this->getProperty('_collectionFactory', $instance);

        $this->assertInstanceOf(Grid::class, $instance);
        $this->assertInstanceOf(CollectionFactory::class, $collectionFactoryProperty);
    }
}
