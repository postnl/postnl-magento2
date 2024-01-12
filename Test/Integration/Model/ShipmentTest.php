<?php

namespace TIG\PostNL\Test\Integration\Model;

use Magento\Framework\App\State;
use Magento\Framework\Event\Config;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\ResourceModel\Shipment\Collection;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Test\Integration\TestCase;

class ShipmentTest extends TestCase
{
    public function testSetConfirmedAtBeforeObserverIsOnlyAvailableInAdmin()
    {
        $config = $this->objectManager->get(Config::class);

        $this->objectManager->get(State::class)->setAreaCode('adminhtml');

        $result = $config->getObservers('tig_postnl_set_confirmed_at_before');

        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * Test if the correct Magento Shipment and Order are loaded from the PostNL Shipment,
     * rather than an existing one from Magento's singleton.
     *
     * @magentoDbIsolation enabled
     */
    public function testMagentoShipmentAndOrderAreLoadedCorrectly()
    {
        include __DIR__ . '/../../Fixtures/default_rollback.php';

        $postnlShipment1 = $this->createShipment();
        $postnlShipment2 = $this->createShipment();

        $this->assertNotEquals($postnlShipment1->getId(), $postnlShipment2->getId());
        $this->assertNotEquals($postnlShipment1->getData(), $postnlShipment2->getData());

        $shipment1 = $postnlShipment1->getShipment();
        $shipment2 = $postnlShipment2->getShipment();

        $this->assertNotEquals($shipment1->getId(), $shipment2->getId());
        $this->assertNotEquals($shipment1->getData(), $shipment2->getData());

        $order1 = $shipment1->getOrder();
        $order2 = $shipment2->getOrder();

        $this->assertNotEquals($order1->getId(), $order2->getId());
        $this->assertNotEquals($order1->getData(), $order2->getData());
    }

    /**
     * @return Shipment
     */
    private function createShipment()
    {
        include __DIR__ . '/../../Fixtures/Shipments/PostNLShipment.php';

        /** @var Collection $shipmentCollection */
        $shipmentCollection = $this->getObject(Collection::class);
        $shipmentCollection->addOrder('entity_id', Collection::SORT_ORDER_DESC);

        $shipment = $shipmentCollection->getFirstItem();

        return $shipment;
    }

    /**
     *  Test if the extra cover amount is gathered correctly from shipments
     *  TODO: Write tests for bundle products and configurable products
     */
    public function testExtraCoverAmount()
    {
        $postnlShipment = include __DIR__ . '/../../Fixtures/Shipments/PostNLShipment.php';

        $this->assertEquals($postnlShipment->getExtraCoverAmount(), 10);
    }
}
