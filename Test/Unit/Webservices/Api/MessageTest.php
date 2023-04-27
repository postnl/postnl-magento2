<?php

namespace TIG\PostNL\Test\Unit\Webservices\Api;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Helper\Data as postNLhelper;

class MessageTest extends TestCase
{
    protected $instanceClass  = Message::class;

    public function testGet()
    {
        $messageIdString = 'string';

        $helperMock = $this->getFakeMock(postNLhelper::class)->getMock();
        $getCurrentTimeStampExpects = $helperMock->expects($this->once());
        $getCurrentTimeStampExpects->method('getCurrentTimeStamp');
        $getCurrentTimeStampExpects->willReturn('31-12-1969 16:00:00');

        $instance = $this->getInstance(
            ['postNLhelper' => $helperMock]
        );
        $this->setProperty('messageIdStrings', ['' => $messageIdString], $instance);

        $result = $instance->get('');

        $this->assertEquals([
            'MessageID' => 'b45cffe084dd3d20d928bee85e7b0f21',
            'MessageTimeStamp' => '31-12-1969 16:00:00',
        ], $result);
    }

    public function getExtraProvider()
    {
        return [
            [['extraTest' => 'test']],
            [['Message' => 'test', 'extraTest' => 'test']],
        ];
    }

    /**
     * @dataProvider getExtraProvider
     *
     * @param $arrayMustContain
     */
    public function testGetExtra($arrayMustContain)
    {
        $instance = $this->getInstance();
        $result = $instance->get('', $arrayMustContain);

        foreach ($arrayMustContain as $key => $value) {
            $this->assertArrayHasKey($key, $result);
            $this->assertEquals($value, $result[$key]);
        }
    }
}
