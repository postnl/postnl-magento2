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

namespace TIG\PostNL\Controller\Unit\Integration\Adminhtml\Shipment;

use Magento\TestFramework\TestCase\AbstractBackendController;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Model\ShipmentRepository;

class ChangeConfirmationTest extends AbstractBackendController
{
    public function testResetsTheMainBarcode()
    {
        if (getenv('TRAVIS') !== false) {
            $this->markTestSkipped('Fails on Travis');
        }

        /** @var ShipmentRepository $repository */
        $repository = $this->_objectManager->get(ShipmentRepository::class);

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = require __DIR__ . '/../../../../Fixtures/Shipments/Shipment.php';

        /** @var Shipment $model */
        $model = $this->_objectManager->get(ShipmentFactory::class)->create();
        $model->setMainBarcode('ABCDEFGHI1234567890');
        $repository->save($model);

        $this->_objectManager->get('Magento\Backend\Model\UrlInterface')->turnOffSecretKey();

        $this->dispatch(
            'backend/postnl/shipment/ChangeConfirmation/postnl_shipment_id/' . $model->getId() . '/shipment_id/'
            . $shipment->getId()
        );

        /**
         * Reload the model from the database
         *
         * @var Shipment
         */
        $model = $repository->getById($model->getId());

        $this->assertNull($model->getMainBarcode());
    }
}
