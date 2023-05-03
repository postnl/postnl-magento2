<?php

namespace TIG\PostNL\Test\Fixtures\Timeframes\Options;

class DataProvider
{
    public function allOptions()
    {
        return [
            'when all options are enabled'  => [true, $this->getOptions(), $this->getOptions()],
            'when all options are disabled' => [false, $this->getOptions(), $this->getOptionsWhenAllAreDisabled()]
        ];
    }

    public function evening()
    {
        return [
            'evening is disabled' => [false, $this->getOptions(), $this->getOptionsForEveningDisabled()],
            'evening is enabled'  => [true, $this->getOptions(), $this->getOptions()]
        ];
    }

    public function sunday()
    {
        return [
            'sunday is disabled' => [false, $this->getOptions(), $this->getOptionsForSundayDisabled()],
            'sunday is enabled'  => [true, $this->getOptions(), $this->getOptions()]
        ];
    }

    private function getOptions()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '18:00:00',
                'Options' => (object)[
                    'string' => ['Evening']
                ],
                'To' => '22:30:00'
            ],
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Sunday']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsForSundayDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '18:00:00',
                'Options' => (object)[
                    'string' => ['Evening']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsForEveningDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Sunday']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsWhenAllAreDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ]
        ];
    }
}
