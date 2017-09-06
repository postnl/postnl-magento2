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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Barcode;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Store\Api\Data\StoreInterface;
use TIG\PostNL\Service\Shipment\Barcode\Range;
use TIG\PostNL\Test\TestCase;

class RangeTest extends TestCase
{
    public $instanceClass = Range::class;

    public function testGetNL()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('NL');

        $expected = [
            'type'  => '3S',
            'range' => '123456',
            'serie' => '000000000-999999999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetEU()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('EU');

        $expected = [
            'type'  => '3S',
            'range' => '123456',
            'serie' => '0000000-9999999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetGlobal()
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->get('global');

        $expected = [
            'type'  => 'CD',
            'range' => '1660',
            'serie' => '0000-9999',
        ];

        $this->assertEquals($expected, $result);
    }

    public function getByCountryProvider()
    {
        return [
            'Netherlands' => ['NL', ['type' => '3S', 'range' => '123456', 'serie' => '000000000-999999999']],
            'EPS Country 1' => ['BE', ['type' => '3S', 'range' => '123456', 'serie' => '0000000-9999999']],
            'EPS Country 2' => ['DE', ['type' => '3S', 'range' => '123456', 'serie' => '0000000-9999999']],
            'Globalpack' => ['US', ['type' => 'CD', 'range' => '1660', 'serie' => '0000-9999']],
        ];
    }

    /**
     * @dataProvider getByCountryProvider
     *
     * @param $country
     * @param $expected
     */
    public function testGetByCountry($country, $expected)
    {
        /** @var Range $instance */
        $instance = $this->getInstance();
        $result = $instance->getByCountryId($country);

        $this->assertEquals($expected, $result);
    }

    /**
     * @return mixed
     */
    public function getInstance(array $args = [])
    {
        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class, true);
        $this->mockFunction($accountConfigurationMock, 'getCustomerCode', '123456');

        $defaultConfigurationMock = $this->getFakeMock(DefaultConfiguration::class, true);
        $this->mockFunction($defaultConfigurationMock, 'getBarcodeGlobalType', 'CD');
        $this->mockFunction($defaultConfigurationMock, 'getBarcodeGlobalRange', '1660');

        $storeInterfaceMock = $this->getFakeMock(StoreInterface::class, true);
        $this->mockFunction($storeInterfaceMock, 'getId', 0);

        $storeManagerMock = $this->getFakeMock(StoreManagerInterface::class, true);
        $this->mockFunction($storeManagerMock, 'getStore', $storeInterfaceMock);

        $instance = parent::getInstance($args + [
            'accountConfiguration' => $accountConfigurationMock,
            'defaultConfiguration' => $defaultConfigurationMock,
            'storeManager'         => $storeManagerMock
        ]);

        return $instance;
    }
}
