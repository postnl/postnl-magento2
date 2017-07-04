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
namespace TIG\PostNL\Test\Unit\Observer\TIGPostNLSetConfirmedAtBefore;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Observer\TIGPostNLSetConfirmedAtBefore\SendTrackAndTraceEmail;
use TIG\PostNL\Test\TestCase;

class SendTrackAndTraceEmailTest extends TestCase
{
    protected $instanceClass = SendTrackAndTraceEmail::class;

    public function testExecute()
    {
        $shipmentMock = $this->getFakeMock(Shipment::class, true);

        $postnlShipmentMock = $this->getFakeMock(PostNLShipment::class)->setMethods(['getShipment'])->getMock();
        $postnlShipmentMock->expects($this->once())->method('getShipment')->willReturn($shipmentMock);

        $trackMock = $this->getFakeMock(Track::class)->setMethods(['send'])->getMock();
        $trackMock->expects($this->once())->method('send')->with($shipmentMock);

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('shipment', $postnlShipmentMock);

        $instance = $this->getInstance(['track' => $trackMock]);
        $instance->execute($observer);
    }
}
