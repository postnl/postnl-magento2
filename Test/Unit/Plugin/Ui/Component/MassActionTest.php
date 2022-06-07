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
namespace TIG\PostNL\Test\Unit\Plugin\Ui\Component;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Plugin\Admin\Ui\Component\MassAction;
use TIG\PostNL\Test\TestCase;

class MassActionTest extends TestCase
{
    public $instanceClass = MassAction::class;

    /**
     * @var ContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $contextMock;

    /**
     * Set up
     */
    public function setUp() : void
    {
        parent::setUp();

        /**
         * Copied from \Magento\Ui\Test\Unit\Component\MassActionTest::setUp();
         */
        $this->contextMock = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->getMockForAbstractClass();
        $processor = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\Processor')
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock->expects($this->any())->method('getProcessor')->willReturn($processor);
    }

    public function testRemovesPostNLOptionsWhenDisabled()
    {
        $massAction = $this->getMassAction(true);

        $expected = [
            'actions' => [
                [
                    'type' => 'random_other_action',
                ],
            ],
        ];

        $this->assertEquals($expected, $massAction->getData('config'));
    }

    public function testDoesNotRemovePostNLOptionsWhenEnabled()
    {
        $massAction = $this->getMassAction(false);

        $expected = [
            'actions' => [
                ['type' => 'random_other_action'],
                ['type' => 'postnl_test_action'],
            ],
        ];

        $this->assertEquals($expected, $massAction->getData('config'));
    }

    /**
     * @param $isEnabled
     *
     * @return \Magento\Ui\Component\MassAction
     * @throws \Exception
     */
    private function getMassAction($isEnabled)
    {
        /** @var \Magento\Ui\Component\MassAction $massAction */
        $massAction = $this->getObject(\Magento\Ui\Component\MassAction::class, [
            'context' => $this->contextMock,
            'data'    => []
        ]);

        $massAction->setData('config', [
            'actions' => [
                ['type' => 'random_other_action'],
                ['type' => 'postnl_test_action'],
            ],
        ]);

        $accountConfigurationMock = $this->getFakeMock(AccountConfiguration::class, true);

        $isModusOff = $accountConfigurationMock->method('isModusOff');
        $isModusOff->willReturn($isEnabled);

        /** @var MassAction $instance */
        $instance = $this->getInstance([
            'accountConfiguration' => $accountConfigurationMock,
        ]);
        $instance->afterPrepare($massAction);

        return $massAction;
    }
}
