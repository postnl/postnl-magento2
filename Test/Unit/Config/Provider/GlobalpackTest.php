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
namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\Globalpack;

class GlobalpackTest extends AbstractConfigurationTest
{
    protected $instanceClass = Globalpack::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsGlobalpackEnabled($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_ENABLED, $value);
        $this->assertEquals($value, $instance->isEnabled());
    }

    public function testGetBarcodeType($type = 'CD')
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_BARCODE_TYPE, $type);
        $this->assertEquals($type, $instance->getBarcodeType());
    }

    public function testGetBarcodeRange($range = '5000')
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_BARCODE_RANGE, $range);
        $this->assertEquals($range, $instance->getBarcodeRange());
    }

    public function testGetLicenseNumber($license = 'test1234')
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_LICENSE_NUMBER, $license);
        $this->assertEquals($license, $instance->getLicenseNumber());
    }

    public function testGetCertificateNumber($certificate = 'test4321')
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_CERTIFICATE_NUMBER, $certificate);
        $this->assertEquals($certificate, $instance->getCertificateNumber());
    }

    public function testGetDefaultShipmentType($type = 'Commercial Goods')
    {
        $instance = $this->getInstance();
        $this->setXpath(Globalpack::XPATH_SHIPMENT_TYPE, $type);
        $this->assertEquals($type, $instance->getDefaultShipmentType());
    }


    public function customsProvider()
    {
        return [
            'use HS Tariff' =>
                ['useHsTariff', Globalpack::XPATH_USE_HS_TARIFF, true],
            'hs tariff attribute - hs disabled' =>
                ['getHsTariffAttributeCode', Globalpack::XPATH_HS_TARIFF_ATTRIBUTE, false, false],
            'hs tariff attribute - hs enabled' =>
                ['getHsTariffAttributeCode', Globalpack::XPATH_HS_TARIFF_ATTRIBUTE, 'custom_attribute_code', true],
            'product value' =>
                ['getProductValueAttributeCode', Globalpack::XPATH_PRODUCT_VALUE_ATTRIBUTE, 'price'],
            'product country of origin' =>
                ['getProductCountryOfOriginAttributeCode', Globalpack::XPATH_PRODUCT_COUNTRY_OF_ORIGIN, 'country_of_manufacture'],
            'product description' =>
                ['getProductDescriptionAttributeCode', Globalpack::XPATH_PRODUCT_DESCRIPTION, 'name'],
            'product sorting attribute' =>
                ['getProductSortingAttributeCode', Globalpack::XPATH_PRODUCT_SORTING, 'price'],
            'product sorting direction' =>
                ['getProductSortingDirection', Globalpack::XPATH_PRODUCT_SORTING_DIRECTION, 'desc'],
        ];
    }

    /**
     * @dataProvider customsProvider
     * @param $method
     * @param $xpath
     * @param $value
     * @param $hsCheck
     */
    public function testCustomsInfo($method, $xpath, $value, $hsCheck = null)
    {
        $instance = $this->getInstance();
        if (!is_null($hsCheck)) {
            $this->setXpathConsecutive([
                [Globalpack::XPATH_USE_HS_TARIFF],
                [$xpath]
            ], [$hsCheck, $value]);
        }

        if (is_null($hsCheck)) {
            $this->setXpath($xpath, $value);
        }

        $this->assertEquals($value, $instance->$method());
    }
}
