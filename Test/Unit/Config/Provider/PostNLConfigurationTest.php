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

use TIG\PostNL\Config\Provider\PostNLConfiguration;

class PostNLConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = PostNLConfiguration::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     *
     * @param $value
     */
    public function testGetStability($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PostNLConfiguration::XPATH_STABILITY, $value);
        $this->assertEquals($value, $instance->getStability());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     *
     * @param $value
     */
    public function testGetSupportedMagentoVersions($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PostNLConfiguration::XPATH_SUPPORTED_MAGENTO_VERSION, $value);
        $this->assertEquals($value, $instance->getSupportedMagentoVersions());
    }
}
