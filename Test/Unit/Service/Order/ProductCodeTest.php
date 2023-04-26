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
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Validation\CountryShipping;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Test\TestCase;

/**
 * Retrieve the product code for a order/shipment.
 */
class ProductCodeTest extends TestCase
{
    const PRODUCT_OPTION_DEFAULT = 'default_product_option';
    const PRODUCT_OPTION_BE_DEFAULT = 'default_be_product_option';
    const PRODUCT_OPTION_BE_DOMESTIC = 'default_be_domestic_product_option';
    const PRODUCT_OPTION_EPS_DEFAULT = '4952';
    const PRODUCT_OPTION_ALTERNATIVE_DEFAULT = 'alternative_default_product_option';
    const PRODUCT_OPTION_EVENING = 'evening_product_option';
    const PRODUCT_OPTION_EXTRAATHOME = 'extraathome_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK = 'pakjegemak_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK_BE = 'pakjegemak_be_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK_BE_DOMESTIC = 'pakjegemak_be_domestic_product_option';
    const PRODUCT_OPTION_SUNDAY = 'sunday_product_option';
    const PRODUCT_OPTION_LETTERBOX_PACKAGE = '2928';

    /**
     * @var ProductOptions|\PHPUnit\Framework\MockObject\MockObject
     */
    private $productOptionsMock;

    /**
     * @var QuoteInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $quoteInterfaceMock;

    public $instanceClass = ProductInfo::class;

    public function setUp() : void
    {
        parent::setUp();

        $this->productOptionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $this->quoteInterfaceMock = $this->getFakeMock(QuoteInterface::class)->getMockForAbstractClass();

        $this->addProductOptionsMockFunction('getDefaultProductOption', static::PRODUCT_OPTION_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultBeProductOption', static::PRODUCT_OPTION_BE_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultBeDomesticProductOption', static::PRODUCT_OPTION_BE_DOMESTIC);
        $this->addProductOptionsMockFunction('getDefaultEpsProductOption', static::PRODUCT_OPTION_EPS_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultEveningProductOption', static::PRODUCT_OPTION_EVENING);
        $this->addProductOptionsMockFunction('getDefaultExtraAtHomeProductOption', static::PRODUCT_OPTION_EXTRAATHOME);
        $this->addProductOptionsMockFunction('getDefaultPakjeGemakProductOption', static::PRODUCT_OPTION_PAKJEGEMAK);
        $this->addProductOptionsMockFunction('getDefaultLetterBoxPackageProductOption', static::PRODUCT_OPTION_LETTERBOX_PACKAGE);
        $this->addProductOptionsMockFunction('getDefaultPakjeGemakBeProductOption', static::PRODUCT_OPTION_PAKJEGEMAK_BE);
        $this->addProductOptionsMockFunction('getDefaultPakjeGemakBeDomesticProductOption', static::PRODUCT_OPTION_PAKJEGEMAK_BE_DOMESTIC);
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
            'default options' => ['', '', 'NL', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'default options BE' => ['', '', 'BE', 'NL', static::PRODUCT_OPTION_BE_DEFAULT, 'Daytime'],
            'default options BE Domestic' => ['', '', 'BE', 'BE', static::PRODUCT_OPTION_BE_DOMESTIC, 'Daytime'],
            'default options DE' => ['', '', 'DE', 'NL', static::PRODUCT_OPTION_EPS_DEFAULT, 'EPS'],
            'default options ES' => ['', '', 'ES', 'NL', static::PRODUCT_OPTION_EPS_DEFAULT, 'EPS'],
            'no option' => ['delivery', '', 'NL', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'default' => ['delivery', 'default', 'NL', 'NL', static::PRODUCT_OPTION_DEFAULT, 'Daytime'],
            'evening' => ['delivery', 'evening', 'NL', 'NL', static::PRODUCT_OPTION_EVENING, 'Evening'],
            'extra at home' => ['delivery', 'extra@home', 'NL', 'NL', static::PRODUCT_OPTION_EXTRAATHOME, 'Extra@Home'],
            'sunday' => ['delivery', 'sunday', 'NL', 'NL', static::PRODUCT_OPTION_SUNDAY, 'Sunday'],
            'default pg' => ['pickup', 'default', 'NL', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK, 'PG'],
            'pakjegemak' => ['pickup', '', 'NL', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK, 'PG'],
            'letterbox package' => ['delivery', 'letterbox_package', 'NL', 'NL', static::PRODUCT_OPTION_LETTERBOX_PACKAGE, 'Letterbox Package'],
            'pakjegemak_be' => ['pickup', '', 'BE', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK_BE, 'PG'],
            'pakjegemak_be_domestic' => ['pickup', '', 'BE', 'BE', static::PRODUCT_OPTION_PAKJEGEMAK_BE_DOMESTIC, 'PG']
        ];
    }

    /**
     * @param $type
     * @param $option
     * @param $country
     * @param $senderCountry
     * @param $expectedCode
     * @param $expectedType
     *
     * @throws \Exception
     * @dataProvider getShippingOptionProvider
     */
    public function testGetShippingOption($type, $option, $country, $senderCountry, $expectedCode, $expectedType)
    {
        $productOptionsFinder = $this->getObject(\TIG\PostNL\Config\Source\Options\ProductOptions::class);
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($country);

        $quoteMock = $this->getFakeMock(Quote::class, true);
        $this->quoteInterfaceMock->method('getQuote')->willReturn($quoteMock);

        $addressConfigurationMock = $this->getFakeMock(AddressConfiguration::class, true);
        $addressConfigurationMock->method('getCountry')->willReturn($senderCountry);

        $countryShipping = $this->getObject(CountryShipping::class, ['addressConfiguration' => $addressConfigurationMock]);

        $instance = $this->getInstance([
            'productOptionsFinder' => $productOptionsFinder,
            'countryShipping' => $countryShipping
        ]);

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
