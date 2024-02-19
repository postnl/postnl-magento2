<?php

namespace TIG\PostNL\Test;

use Magento\Framework\Filesystem;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var null|string
     */
    protected $instanceClass;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @param array $args
     *
     * @return object
     * @throws \Exception
     */
    public function getInstance(array $args = [])
    {
        if (empty($this->instanceClass)) {
            throw new \Exception('The instanceClass property is not set.');
        }

        return $this->getObject($this->instanceClass, $args);
    }

    /**
     * Basic setup
     */
    public function setUp() : void
    {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        $this->objectManager = new ObjectManagerHelper($this);
    }

    /**
     * @param $method
     * @param $instance
     *
     * @return \ReflectionMethod
     */
    protected function getMethod($method, $instance)
    {
        $method = new \ReflectionMethod($instance, $method);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @param      $method
     * @param null $instance
     *
     * @return mixed
     */
    protected function invoke($method, $instance = null)
    {
        if ($instance === null) {
            $instance = $this->getInstance();
        }

        $method = $this->getMethod($method, $instance);

        return $method->invoke($instance);
    }

    /**
     * @param       $method
     * @param array $args
     * @param null  $instance
     *
     * @return mixed
     */
    protected function invokeArgs($method, $args = [], $instance = null)
    {
        if ($instance === null) {
            $instance = $this->getInstance();
        }

        $method = $this->getMethod($method, $instance);

        return $method->invokeArgs($instance, $args);
    }

    /**
     * @param      $property
     * @param null $instance
     *
     * @return \ReflectionProperty
     */
    protected function getProperty($property, $instance = null)
    {
        if ($instance === null) {
            $instance = $this->getInstance();
        }

        $reflection = new \ReflectionObject($instance);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($instance);
    }

    /**
     * @param      $property
     * @param      $value
     * @param null $instance
     *
     * @return \ReflectionProperty
     */
    protected function setProperty($property, $value, $instance = null)
    {
        if ($instance === null) {
            $instance = $this->getInstance();
        }

        $reflection = new \ReflectionObject($instance);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($instance, $value);

        return $property;
    }

    /**
     * @param      $class
     * @param bool $return Immediate call getMock.
     *
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getFakeMock($class, $return = false)
    {
        $mock = $this->getMockBuilder($class);
        $mock->disableOriginalConstructor();

        if ($return) {
            return $mock->getMock();
        }

        return $mock;
    }

    /**
     * @param       $class
     * @param array $args
     *
     * @return object
     * @throws \Exception
     */
    protected function getObject($class, $args = [])
    {
        if ($this->objectManager === null) {
            throw new \Exception('The object manager is not loaded. Dit you forget to call parent::setUp();?');
        }

        return $this->objectManager->getObject($class, $args);
    }

    /**
     * Quickly mock a function.
     *
     * @param                                          $function
     * @param                                          $response
     * @param \PHPUnit\Framework\MockObject\MockObject $instance
     */
    protected function mockFunction(
        $instance,
        $function,
        $response,
        $with = []
    ) {
        $method = $instance->method($function);

        if ($with) {
            $method->with(...$with);
        }

        $method->willReturn($response);
    }

    /**
     * @param $filename
     *
     * @return Filesystem\File\ReadInterface
     */
    protected function loadFile($filename): Filesystem\File\ReadInterface
    {
        /** @var Filesystem $filesystem */
        $filesystem   = $this->getObject(Filesystem::class);
        $directory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);

        $path = __DIR__ . '/' . $filename;
        $path = realpath($path);

        $path = $directory->getRelativePath($path);
        $file = $directory->openFile($path);

        return $file;
    }

    protected function getMagentoVersion()
    {
        /** @var \Magento\Framework\App\ProductMetadataInterface $productMetaData */
        $productMetaData = $this->getObject(\Magento\Framework\App\ProductMetadataInterface::class);

        return $productMetaData->getVersion();
    }

    /**
     * @param $className
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMock($className){
        return $this->getFakeMock($className, true);
    }
}
