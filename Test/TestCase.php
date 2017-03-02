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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test;

use Magento\TestFramework\ObjectManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

abstract class TestCase extends \PHPUnit_Framework_TestCase
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
     */
    public function getInstance(array $args = [])
    {
        return $this->getObject($this->instanceClass, $args);
    }

    /**
     * Basic setup
     */
    public function setUp()
    {
        /** Require functions.php to be able to use the translate function */
        if (strpos(__DIR__, 'vendor') === false) {
            require_once __DIR__ . '/../../../../functions.php';
        } else {
            require_once __DIR__ . '/../../../../app/functions.php';
        }

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
     * @param $class
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder
     */
    protected function getFakeMock($class)
    {
        $mock = $this->getMockBuilder($class);
        $mock->disableOriginalConstructor();

        return $mock;
    }

    protected function getObject($class, $args = [])
    {
        if ($this->objectManager === null) {
            throw new \Exception('The object manager is not loaded. Dit you forget to call parent::setUp();?');
        }

        return $this->objectManager->getObject($class, $args);
    }
}
