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
namespace TIG\PostNL\Test\Integration\Model;

use Magento\Framework\App\State;
use Magento\Framework\Event\ConfigInterface;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Test\Integration\TestCase;

class ShipmentTest extends TestCase
{
    public function testSetConfirmedAtBeforeObserverIsCalled()
    {
        $this->objectManager->get(State::class)->setAreaCode('adminhtml');

        $trackMock = $this->getFakeMock(Track::class)->setMethods(['send'])->getMock();

        $this->objectManager->configure([
            'preferences' => [
                Track::class => get_class($trackMock),
            ],
        ]);

        $model = $this->getNewModel();

        $newTrackMock = $this->objectManager->get(Track::class);
        $newTrackMock->method('send')->with($model->getShipment());

        $model->setConfirmedAt('01-01-1970');
    }

    public function testSetConfirmedAtBeforeObserverIsOnlyAvailableInAdmin()
    {
        $config = $this->objectManager->get(ConfigInterface::class);

        $this->objectManager->get(State::class)->setAreaCode('frontend');
        $frontedResult = $config->getObservers('tig_postnl_set_confirmed_at_before');
        $this->assertCount(0, $frontedResult);

        $this->objectManager->get(State::class)->setAreaCode('adminhtml');
        $adminResult = $config->getObservers('tig_postnl_set_confirmed_at_before');
        $this->assertGreaterThanOrEqual(1, $adminResult);
    }

    /**
     * @return Shipment
     */
    private function getNewModel()
    {
        /** @var ShipmentFactory $factory */
        $factory = $this->getObject(ShipmentFactory::class);

        /** @var Shipment $model */
        $model = $factory->create();

        return $model;
    }
}
