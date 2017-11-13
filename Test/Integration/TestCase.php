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

    public function setUp()
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
     * @return \PHPUnit_Framework_MockObject_MockObject
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
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function disableEndpoint($endpoint, $response = null)
    {
        $mock = $this->putMockInObjectManager($endpoint);

        $call = $mock->method('call');
        $call->willReturn($response);

        return $mock;
    }

    protected function tearDown()
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
