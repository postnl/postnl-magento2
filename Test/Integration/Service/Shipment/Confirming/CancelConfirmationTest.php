<?php

namespace TIG\PostNL\Test\Integration\Service\Shipment\Confirming;

use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use TIG\PostNL\Test\Integration\TestCase;

class CancelConfirmationTest extends TestCase
{
    public $instanceClass = ResetPostNLShipment::class;
    
    /**
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testResetPostNLShipment()
    {
        $postNLShipment = require realpath(__DIR__ . '/../../../../Fixtures/Shipments/PostNLShipment.php');
        $postNLShipment->setConfirmedAt('2018-06-06 12:39:56');
        $postNLShipment->setMainBarcode('ABCDEFGHI1234567890');
        $postNLShipment->save();
        /** @var ResetPostNLShipment $instance */
        $instance = $this->getInstance();

        $instance->resetShipment($postNLShipment->getShipmentId());

        $resetPostNLShipment = $postNLShipment->load($postNLShipment->getId());

        $this->assertNull($resetPostNLShipment->getMainBarcode());
        $this->assertNull($resetPostNLShipment->getConfirmedAt());
    }
}