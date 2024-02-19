<?php

namespace TIG\PostNL\Test\Unit\Config\Csv;

use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use TIG\PostNL\Config\Csv\Import\Tablerate;
use TIG\PostNL\Model\ResourceModel\Tablerate as TablerateResource;
use TIG\PostNL\Model\ResourceModel\TablerateFactory;
use TIG\PostNL\Service\Import\Csv;
use TIG\PostNL\Test\TestCase;

class TablerateTest extends TestCase
{
    protected $instanceClass = Tablerate::class;

    public function testAfterSave()
    {
        $tablerateMock = $this->getFakeMock(TablerateResource::class);
        $tablerateMock->setMethods(['getConditionName', 'uploadAndImport']);
        $tablerateMock = $tablerateMock->getMock();

        $tablerateMock->expects($this->once())->method('getConditionName');
        $tablerateMock->expects($this->once())->method('uploadAndImport');

        $tablerateFactoryMock = $this->getFakeMock(TablerateFactory::class)->setMethods(['create'])->getMock();
        $tablerateFactoryMock->expects($this->once())->method('create')->willReturn($tablerateMock);

        $instance = $this->getInstance(['tablerateFactory' => $tablerateFactoryMock]);
        $result = $instance->afterSave();

        $this->assertInstanceOf(Tablerate::class, $result);
    }

    /**
     * @return array
     */
    public function getCsvDataProvider()
    {
        return [
            'no_file_uploaded' => [
                null,
                []
            ],
            'file_uploaded_with_data' => [
                'datafile.csv',
                [
                    'content data',
                    'content data'
                ]
            ],
            'file_uploaded_without_data' => [
                'datafile.csv',
                []
            ],
        ];
    }

    /**
     * @param $file
     * @param $data
     *
     * @dataProvider getCsvDataProvider
     */
    public function testGetCsvData($file, $data)
    {
        $expectedCallCount = (int)(null !== $file);

        // @codingStandardsIgnoreLine
        $_FILES['groups']['tmp_name']['tig_postnl']['fields']['tablerate_import']['value'] = $file;

        $websiteMock = $this->getFakeMock(WebsiteInterface::class)->getMock();
        $websiteMock->expects($this->exactly($expectedCallCount))->method('getId')->willReturn('1');

        $storeManagerMock = $this->getFakeMock(StoreManagerInterface::class)->getMock();
        $storeManagerMock->expects($this->exactly($expectedCallCount))->method('getWebsite')->willReturn($websiteMock);

        $csv = $this->getFakeMock(Csv::class)->setMethods(['getData'])->getMock();
        $csv->expects($this->exactly($expectedCallCount))->method('getData')->willReturn($data);

        $instance = $this->getInstance(['storeManager' => $storeManagerMock, 'csv' => $csv]);
        $result = $this->invokeArgs('getCsvData', [1, 'package_value'], $instance);

        $this->assertIsArray($result);
    }
}
