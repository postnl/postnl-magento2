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
namespace TIG\PostNL\Unit\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use TIG\PostNL\Model\ResourceModel\Tablerate;
use TIG\PostNL\Test\TestCase;

class TablerateTest extends TestCase
{
    protected $instanceClass = Tablerate::class;

    /**
     * @return array
     */
    public function getConditionNameProvider()
    {
        return [
            'fromDataObject' => [
                'abc',
                'def',
                '0',
                'abc'
            ],
            'fromCoreConfig' => [
                'ghi',
                'jkl',
                '1',
                'jkl'
            ],
        ];
    }

    /**
     * @param $dataValue
     * @param $configValue
     * @param $inherit
     * @param $expects
     *
     * @dataProvider getConditionNameProvider
     */
    public function testGetConditionName($dataValue, $configValue, $inherit, $expects)
    {
        $dataObject = $this->getObject(DataObject::class);
        $dataObject->setData([
            'groups' => [
                'tig_postnl' => [
                    'fields' => [
                        'condition_name' => [
                            'value' => $dataValue,
                            'inherit' => $inherit
                        ]
                    ]
                ]
            ]
        ]);

        $scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class);
        $scopeConfigMock->setMethods(['getValue', 'isSetFlag']);
        $scopeConfigMock = $scopeConfigMock->getMock();

        $getValueExpects = $scopeConfigMock->expects($this->exactly((int)$inherit));
        $getValueExpects->method('getValue');
        $getValueExpects->with('carriers/tig_postnl/condition_name', 'default');
        $getValueExpects->willReturn($configValue);


        $instance = $this->getInstance(['coreConfig' => $scopeConfigMock]);
        $result = $instance->getConditionName($dataObject);

        $this->assertEquals($expects, $result);
    }

    /**
     * TODO: current test validates when nothing is uploaded. Add test when something IS uploaded.
     */
    public function testUploadAndImport()
    {
        $dataObject = $this->getObject(DataObject::class);

        $instance = $this->getInstance();
        $result = $instance->uploadAndImport($dataObject);

        $this->assertEquals($instance, $result);
    }
}
