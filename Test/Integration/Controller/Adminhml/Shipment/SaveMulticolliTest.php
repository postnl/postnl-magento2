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

namespace Integration\Controller\Adminhml\Shipment;

use Magento\TestFramework\TestCase\AbstractBackendController;

class SaveMulticolliTest extends AbstractBackendController
{
    public function testSavesTheMulticolli()
    {
        if (getenv('TRAVIS') !== false) {
            $this->markTestSkipped('Fails on Travis');
        }
        
        $postnlShipment = require realpath(__DIR__ . '/../../../../Fixtures/Shipments/PostNLShipment.php');

        $this->getRequest()->setPostValue([
            'shipmentId' => $postnlShipment->getId(),
            'parcelCount' => 99,
        ]);

        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();

        $this->dispatch('backend/postnl/shipment/SaveMulticolli');

        /** @var \TIG\PostNL\Model\Shipment $newPostnlShipment */
        $newPostnlShipment = $this->_objectManager->get(\TIG\PostNL\Model\Shipment::class);
        $newPostnlShipment->load($postnlShipment->getId());

        $response = json_decode($this->getResponse()->getBody(), true);
        $this->assertTrue($response['success']);

        $this->assertEquals(99, $newPostnlShipment->getParcelCount());
    }

    public function testDoesNotSaveTheMulticolliWhenConfirmed()
    {
        if (getenv('TRAVIS') !== false) {
            $this->markTestSkipped('Fails on Travis');
        }

        $postnlShipment = require realpath(__DIR__ . '/../../../../Fixtures/Shipments/PostNLShipment.php');
        $postnlShipment->setConfirmedAt('2016-11-19 21:13:13');
        $postnlShipment->setParcelCount(1);
        $postnlShipment->save();

        $this->getRequest()->setPostValue([
            'shipmentId' => $postnlShipment->getId(),
            'parcelCount' => 99,
        ]);

        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();

        $this->dispatch('backend/postnl/shipment/SaveMulticolli');

        /** @var \TIG\PostNL\Model\Shipment $newPostnlShipment */
        $newPostnlShipment = $this->_objectManager->get(\TIG\PostNL\Model\Shipment::class);
        $newPostnlShipment->load($postnlShipment->getId());

        $response = json_decode($this->getResponse()->getBody(), true);
        $this->assertFalse($response['success']);

        $this->assertEquals(1, $newPostnlShipment->getParcelCount());
    }
}
