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
namespace TIG\PostNL\Unit\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
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
     * TODO: Currently only tests when no data has been uploaded. Add tests when data IS uploaded.
     */
    public function testUploadAndImport()
    {
        $dataObject = $this->getObject(DataObject::class);

        $instance = $this->getInstance();
        $result = $instance->uploadAndImport($dataObject, []);

        $this->assertEquals($instance, $result);
    }

    public function testDeleteByCondition()
    {
        $connectionMock = $this->getFakeMock(AdapterInterface::class)->getMock();
        $connectionMock->expects($this->once())->method('beginTransaction');
        $connectionMock->expects($this->once())->method('delete');
        $connectionMock->expects($this->once())->method('commit');

        $resourceMock = $this->getFakeMock(ResourceConnection::class)->setMethods(['getConnection', 'getTableName']);
        $resourceMock = $resourceMock->getMock();
        $resourceMock->expects($this->once())->method('getConnection')->willReturn($connectionMock);
        $resourceMock->expects($this->once())->method('getTableName');

        $context = $this->getFakeMock(Context::class)->setMethods(['getResources'])->getMock();
        $context->expects($this->once())->method('getResources')->willReturn($resourceMock);

        $websiteMock = $this->getFakeMock(WebsiteInterface::class)->getMock();
        $websiteMock->expects($this->once())->method('getId')->willReturn('1');

        $storeManagerMock = $this->getFakeMock(StoreManagerInterface::class)->getMock();
        $storeManagerMock->expects($this->once())->method('getWebsite')->willReturn($websiteMock);

        $dataObject = $this->getObject(DataObject::class);

        $instance = $this->getInstance(['context' => $context, 'storeManager' => $storeManagerMock]);
        $this->invokeArgs('deleteByCondition', [$dataObject], $instance);
    }

    /**
     * @return array
     */
    public function importDataProvider()
    {
        return [
            'succesfull' => [
                null,
                null
            ],
            'exception' => [
                '\TIG\PostNL\Exception',
                '[POSTNL-0251] An error occurred while importing the table rates.'
            ],
        ];
    }

    /**
     * @param $exceptionType
     * @param $exceptionMessage
     *
     * @throws \Exception
     *
     * @dataProvider importDataProvider
     */
    public function testImportData($exceptionType, $exceptionMessage)
    {
        $connectionMock = $this->getFakeMock(AdapterInterface::class)->getMock();
        $connectionMock->expects($this->once())->method('beginTransaction');
        $connectionMock->expects($this->exactly((int)(null === $exceptionType)))->method('commit');
        $connectionMock->expects($this->exactly((int)(null !== $exceptionType)))->method('rollback');

        $expectsInsertArray = $connectionMock->expects($this->once())->method('insertArray');
        if (null !== $exceptionType) {
            $expectsInsertArray->willThrowException(new $exceptionType(__('')));
        }

        $resourceMock = $this->getFakeMock(ResourceConnection::class)->setMethods(['getConnection', 'getTableName']);
        $resourceMock = $resourceMock->getMock();
        $resourceMock->expects($this->exactly(2))->method('getConnection')->willReturn($connectionMock);
        $resourceMock->expects($this->once())->method('getTableName');

        $context = $this->getFakeMock(Context::class)->setMethods(['getResources'])->getMock();
        $context->expects($this->once())->method('getResources')->willReturn($resourceMock);

        $instance = $this->getInstance(['context' => $context]);

        try {
            $this->invokeArgs('importData', [['columns' => ['abc'], 'records' => [['def']]]], $instance);

            $this->assertNull($exceptionType);
            $this->assertNull($exceptionMessage);
        } catch (\Exception $exception) {
            if (null === $exceptionType || null === $exceptionMessage) {
                throw $exception;
            }

            $this->assertEquals($exceptionMessage, $exception->getMessage());
            $this->assertInstanceOf($exceptionType, $exception);
        }
    }
}
