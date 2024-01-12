<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Grid\Shipment;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Block\Adminhtml\Grid\Shipment\ConfirmStatus;

class ConfirmStatusTest extends TestCase
{
    protected $instanceClass = ConfirmStatus::class;

    public function getIsConfirmedProvider()
    {
        return [
            'exists_but_not_confirmed' => [null, false],
            'exists_and_confirmed' => ['2016-11-19 21:13:12', true],
        ];
    }

    /**
     * @param $confirmedAt
     * @param $expected
     *
     * @dataProvider getIsConfirmedProvider
     */
    public function testGetCellContents($confirmedAt, $expected)
    {
        $item = ['tig_postnl_confirmed_at' => $confirmedAt];

        $instance = $this->getFakeMock($this->instanceClass)->getMock();

        /** @var \Magento\Framework\Phrase $result */
        $result = $this->invokeArgs('getCellContents', [$item], $instance);

        $this->assertInstanceOf(\Magento\Framework\Phrase::class, $result);
        $text = ucfirst(($expected ? '' : 'not ') . 'confirmed');
        $this->assertEquals($text, $result->getText());
    }
}
