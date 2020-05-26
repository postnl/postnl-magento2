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
namespace TIG\PostNL\Test\Unit\Service\Order;

use Magento\Quote\Model\Quote;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Test\TestCase;

/**
 * Retrieve the product code for a order/shipment.
 */
class ProductCodeTest extends TestCase
{
    const PRODUCT_OPTION_DEFAULT = 'default_product_option';
    const PRODUCT_OPTION_BE_DEFAULT = 'default_be_product_option';
    const PRODUCT_OPTION_EPS_DEFAULT = '4952';
    const PRODUCT_OPTION_ALTERNATIVE_DEFAULT = 'alternative_default_product_option';
    const PRODUCT_OPTION_EVENING = 'evening_product_option';
    const PRODUCT_OPTION_EXTRAATHOME = 'extraathome_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK = 'pakjegemak_product_option';
    const PRODUCT_OPTION_SUNDAY = 'sunday_product_option';
    const PRODUCT_OPTION_LETTERBOX_PACKAGE = '2928';

    /**
     * @var ProductOptions|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productOptionsMock;

    /**
     * @var QuoteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $quoteInterfaceMock;

    public $instanceClass = ProductInfo::class;

    public function setUp()
    {
        parent::setUp();

        $this->productOptionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $this->quoteInterfaceMock = $this->getFakeMock(QuoteInterface::class)->getMockForAbstractClass();

        $this->addProductOptionsMockFunction('getDefaultProductOption', static::PRODUCT_OPTION_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultBeProductOption', static::PRODUCT_OPTION_BE_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultEpsProductOption', static::PRODUCT_OPTION_EPS_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultEveningProductOption', static::PRODUCT_OPTION_EVENING);
        $this->addProductOptionsMockFunction('getDefaultExtraAtHomeProductOption', static::PRODUCT_OPTION_EXTRAATHOME);
        $this->addProductOptionsMockFunction('getDefaultPakjeGemakProductOption', static::PRODUCT_OPTION_PAKJEGEMAK);
        $this->addProductOptionsMockFunction('getDefaultSundayProductOption', static::PRODUCT_OPTION_SUNDAY);
        $this->addProductOptionsMockFunction(
            'getAlternativeDefaultProductOption',
            static::PRODUCT_OPTION_ALTERNATIVE_DEFAULT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInstance(array $args = [])
    {
        if (!isset($args['productOptionsConfiguration'])) {
            $args['productOptionsConfiguration'] = $this->productOptionsMock;
        }

        if (!isset($args['quote'])) {
            $args['quote'] = $this->quoteInterfaceMock;
        }

        return parent::getInstance($args);
    }

    /**
     * @return array
     */
    public function getShippingOptionProvider()
    {
        return [
            'default options' => ['', '', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'default options BE' => ['', '', 'BE', static::PRODUCT_OPTION_BE_DEFAULT, 'Daytime'],
            'default options DE' => ['', '', 'DE', static::PRODUCT_OPTION_EPS_DEFAULT, 'EPS'],
            'default options ES' => ['', '', 'ES', static::PRODUCT_OPTION_EPS_DEFAULT, 'EPS'],
            'no option' => ['delivery', '', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'default' => ['delivery', 'default', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'evening' => ['delivery', 'evening', 'NL', static::PRODUCT_OPTION_EVENING, 'Evening'],
            'extra at home' => ['delivery', 'extra@home', 'NL', static::PRODUCT_OPTION_EXTRAATHOME, 'Extra@Home'],
            'sunday' => ['delivery', 'sunday', 'NL', static::PRODUCT_OPTION_SUNDAY, 'Sunday'],
            'default pg' => ['pickup', 'default', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK, 'PG'],
            'pakjegemak' => ['pickup', '', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK, 'PG'],
            'letterbox package' => ['delivery', 'letterbox_package', 'NL', static::PRODUCT_OPTION_LETTERBOX_PACKAGE, 'Letter Box']
        ];
    }

    /**
     * @param $type
     * @param $option
     * @param $country
     * @param $expectedCode
     * @param $expectedType
     *
     * @dataProvider getShippingOptionProvider
     */
    public function testGetShippingOption($type, $option, $country, $expectedCode, $expectedType)
    {
        $productOptionsFinder = $this->getObject(\TIG\PostNL\Config\Source\Options\ProductOptions::class);
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($country);

        $quoteMock = $this->getFakeMock(Quote::class, true);
        $this->quoteInterfaceMock->method('getQuote')->willReturn($quoteMock);

        $instance = $this->getInstance(['productOptionsFinder' => $productOptionsFinder]);

        $result = $instance->get($type, $option, $address);
        $this->assertEquals($expectedCode, $result['code']);
        $this->assertEquals($expectedType, $result['type']);
    }

    /**
     * @return array
     */
    public function getDefaultProductOptionProvider()
    {
        return [
            'alternative disabled' => [
                0,
                5,
                10,
                static::PRODUCT_OPTION_DEFAULT
            ],
            'alternative enabled, amount limit not exceeded' => [
                1,
                20,
                15,
                static::PRODUCT_OPTION_DEFAULT
            ],
            'alternative enabled, amount limit exceeded' => [
                1,
                25,
                30,
                static::PRODUCT_OPTION_ALTERNATIVE_DEFAULT
            ],
            'alternative enabled, amount limit equals quote total' => [
                1,
                40,
                40,
                static::PRODUCT_OPTION_ALTERNATIVE_DEFAULT
            ]
        ];
    }

    /**
     * @param $useAlternative
     * @param $alternativeMinAmount
     * @param $quoteTotal
     * @param $expected
     *
     * @dataProvider getDefaultProductOptionProvider
     */
    public function testGetDefaultProductOption($useAlternative, $alternativeMinAmount, $quoteTotal, $expected)
    {
        $quoteMock = $this->getFakeMock(Quote::class)->setMethods(['getBaseGrandTotal'])->getMock();
        $quoteMock->expects($this->once())->method('getBaseGrandTotal')->willReturn($quoteTotal);

        $this->quoteInterfaceMock->method('getQuote')->willReturn($quoteMock);
        $this->productOptionsMock->method('getUseAlternativeDefault')->willReturn($useAlternative);
        $this->productOptionsMock->method('getAlternativeDefaultMinAmount')->willReturn($alternativeMinAmount);

        $instance = $this->getInstance();
        $this->invokeArgs('setDefaultProductOption', ['country' => 'NL'], $instance);

        $resultCode = $this->getProperty('code', $instance);
        $resultType = $this->getProperty('type', $instance);

        $this->assertEquals($expected, $resultCode);
        $this->assertEquals('Daytime', $resultType);
    }

    private function addProductOptionsMockFunction($function, $returnValue)
    {
        $expects = $this->productOptionsMock->method($function);
        $expects->willReturn($returnValue);
    }
}
