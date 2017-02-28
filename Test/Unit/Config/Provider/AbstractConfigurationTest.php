<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Config\Provider\PostNLConfiguration;
use TIG\PostNL\Test\TestCase;

abstract class AbstractConfigurationTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    protected function initScopeConfigMock()
    {
        $this->scopeConfigMock = $this->getMock(ScopeConfigInterface::class);
    }

    /**
     * @param array $args
     *
     * @return DefaultConfiguration|PostNLConfiguration
     */
    public function getInstance(array $args = [])
    {
        $this->initScopeConfigMock();

        $args['scopeConfig'] = $this->scopeConfigMock;

        return parent::getInstance($args);
    }

    /**
     * @param      $xpath
     * @param      $value
     * @param null $storeId
     * @param null $matcher
     */
    protected function setXpath($xpath, $value, $storeId = null, $matcher = null)
    {
        if ($matcher === null) {
            $matcher = $this->once();
        }

        $getValueExpects = $this->scopeConfigMock->expects($matcher);
        $getValueExpects->method('getValue');
        $getValueExpects->with(
            $xpath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $getValueExpects->willReturn($value);
    }
}
