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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Integration\Service\Shipment\Confirming;

use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use TIG\PostNL\Test\Integration\TestCase;

class ChangeConfirmationTest extends TestCase
{
    public $instanceClass = ResetPostNLShipment::class;

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