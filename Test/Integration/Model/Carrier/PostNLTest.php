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

namespace TIG\PostNL\Test\Integration\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Model\Carrier\PostNL;
use TIG\PostNL\Service\Import\Matrixrate\Data;
use TIG\PostNL\Test\Integration\TestCase;

class PostNLTest extends TestCase
{
    public $instanceClass = PostNL::class;

    /**
     * @var \Magento\TestFramework\App\Config $config
     */
    private $config;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest
     */
    private $request;

    /**
     * @var \TIG\PostNL\Service\Carrier\Price\Calculator
     */
    private $calculator;

    public function setUp()
    {
        parent::setUp();

        $this->config = $this->getObject(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $this->calculator = $this->getObject(\TIG\PostNL\Service\Carrier\Price\Calculator::class, [
            'scopeConfig' => $this->config,
        ]);

        $this->request = $this->getObject(RateRequest::class);
        $this->request->setDestCountryId('NL');
        $this->request->setDestRegionId(0);
        $this->request->setPackageQty(2);
        $this->request->setWebsiteId(1);
        $this->request->setPackageWeight(100);
        $this->request->setPackageValue(100);
    }

    public function getInstance(array $args = [])
    {
        $accountConfig = $this->getMockBuilder(\TIG\PostNL\Config\Provider\AccountConfiguration::class);
        $accountConfig->disableOriginalConstructor();
        $accountConfig = $accountConfig->getMock();

        $this->objectManager->configure([
            'preferences' => [
                \TIG\PostNL\Config\Provider\AccountConfiguration::class => get_class($accountConfig)
            ]
        ]);

        $accountConfig = $this->objectManager->get(\TIG\PostNL\Config\Provider\AccountConfiguration::class);
        $accountConfig->method('isModusOff')->willReturn(false);

        $args['scopeConfig'] = $this->config;
        $args['calculator'] = $this->calculator;
        return parent::getInstance($args);
    }

    private function loadMatrixrates()
    {
        $file = $this->loadFile('Fixtures/Matrixrate/pricing.csv');
        $this->getObject(Data::class)->import($file);
        $file->close();
    }

    public function testIsInactive()
    {
        if (!version_compare($this->getMagentoVersion(), '2.1.3', '>')) {
            $this->markTestSkipped('We expect the \Magento\TestFramework\App\Config in $this->config, but this was' .
                'only introduced in 2.1.3. That\'s why we skip this test for lower versions.');
        }

        $this->config->setValue('carriers/tig_postnl/active', false, ScopeInterface::SCOPE_STORE);

        /** @var PostNL $instance */
        $instance = $this->getInstance();
        $result = $instance->collectRates($this->request);

        $this->assertFalse($result);
    }

    public function testGetRates()
    {
        /** @var PostNL $instance */
        $instance = $this->getInstance();
        $result = $instance->collectRates($this->request);

        $rates = $result->getAllRates();
        $rate = $result->getCheapestRate();
        $this->assertNull($result->getError());
        $this->assertCount(1, $rates);
        $this->assertEquals('tig_postnl', $rate->getData('carrier'));
        $this->assertEquals('Verzenden via PostNL', $rate->getData('carrier_title'));
        $this->assertEquals('regular', $rate->getData('method'));
        $this->assertEquals('PostNL', $rate->getData('method_title'));
        $this->assertEquals(5, $rate->getData('price'));
        $this->assertEquals(5, $rate->getData('cost'));
    }

    public function getRatesWithMatrixrateProvider()
    {
        return [
            'NL'          => ['NL', 0, 5],
            'FR'          => ['FR', 0, 10],
            'MC'          => ['MC', 0, 10],
            'ES region 1' => ['ES', 157, 15],
            'ES region 2' => ['ES', 170, 20],
        ];
    }

    /**
     * @param $countryId
     * @param $regionId
     * @param $expected
     *
     * @dataProvider getRatesWithMatrixrateProvider
     */
    public function testGetRatesWithMatrixrate($countryId, $regionId, $expected)
    {
        if (!version_compare($this->getMagentoVersion(), '2.1.3', '>')) {
            $this->markTestSkipped('We expect the \Magento\TestFramework\App\Config in $this->config, but this was' .
                'only introduced in 2.1.3. That\'s why we skip this test for lower versions.');
        }

        $this->loadMatrixrates();

        $this->config->setValue(
            'carriers/tig_postnl/rate_type',
            RateType::CARRIER_RATE_TYPE_MATRIX,
            ScopeInterface::SCOPE_STORE
        );

        $this->request->setDestCountryId($countryId);
        $this->request->setDestRegionId($regionId);

        /** @var PostNL $instance */
        $instance = $this->getInstance();
        $result = $instance->collectRates($this->request);

        $rate = $result->getCheapestRate();
        $this->assertEquals($expected, $rate->getData('price'));
    }

    public function testWhenNoMatrixrateFoundTheDefaultIsUsed()
    {
        if (!version_compare($this->getMagentoVersion(), '2.1.3', '>')) {
            $this->markTestSkipped('We expect the \Magento\TestFramework\App\Config in $this->config, but this was' .
                'only introduced in 2.1.3. That\'s why we skip this test for lower versions.');
        }

        $this->config->setValue('carriers/tig_postnl/price', '123123', ScopeInterface::SCOPE_STORE);
        $this->request->setDestRegionId(123123);

        /** @var PostNL $instance */
        $instance = $this->getInstance();
        $result = $instance->collectRates($this->request);

        $rates = $result->getAllRates();
        $rate = array_shift($rates);
        $this->assertEquals(123123, $rate->getData('price'));
        $this->assertEquals(123123, $rate->getData('cost'));
    }
}
