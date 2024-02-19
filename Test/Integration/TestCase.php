<?php

namespace TIG\PostNL\Test\Integration;

use Magento\TestFramework\ObjectManager;
use TIG\PostNL\Test\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $swappedClasses = [];

    public function setUp() : void
    {
        parent::setUp();

        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * Create a new object of type $class. It will use new to create an object.
     *
     * @param       $class
     * @param array $args
     *
     * @return mixed
     */
    public function getObject($class, $args = [])
    {
        return $this->objectManager->create($class, $args);
    }

    /**
     * Load an object using the object manager. If it not instantiated yet it will create a new object. If it is
     * already instantiated by the object manager it will return that object.
     *
     * @param $class
     *
     * @return mixed
     */
    public function loadObject($class)
    {
        return $this->objectManager->get($class);
    }

    /**
     * @param $class
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function putMockInObjectManager($class)
    {
        $this->swappedClasses[$class] = $class;

        $instance = $this->getMockBuilder($class);
        $instance->disableOriginalConstructor();
        $instance = $instance->getMock();

        $this->objectManager->configure([
            'preferences' => [
                $class => get_class($instance),
            ],
        ]);

        return $this->objectManager->get($class);
    }

    /**
     * @param      $endpoint
     * @param null $response
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function disableEndpoint($endpoint, $response = null)
    {
        $mock = $this->putMockInObjectManager($endpoint);

        $call = $mock->method('call');
        $call->willReturn($response);

        return $mock;
    }

    protected function tearDown() : void
    {
        if (!count($this->swappedClasses)) {
            return;
        }

        $this->objectManager->configure([
            'preferences' => $this->swappedClasses,
        ]);

        parent::tearDown();
    }

}
