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
namespace TIG\PostNL\Service\Shipment\Labelling;

use TIG\PostNL\Service\Shipment\Labelling\Generate\WithoutConfirm;
use TIG\PostNL\Service\Shipment\Labelling\Generate\WithConfirm;
use TIG\PostNL\Api\Data\ShipmentInterface;

class GenerateLabel
{
    /**
     * @var WithoutConfirm
     */
    private $withoutConfirm;

    /**
     * @var WithConfirm
     */
    private $withConfirm;

    /**
     * @param WithConfirm    $withConfirm
     * @param WithoutConfirm $withoutConfirm
     */
    public function __construct(
        WithConfirm $withConfirm,
        WithoutConfirm $withoutConfirm
    ) {
        $this->withConfirm    = $withConfirm;
        $this->withoutConfirm = $withoutConfirm;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentShipmentNumber
     * @param bool              $confirm
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function get(ShipmentInterface $shipment, $currentShipmentNumber, $confirm)
    {
        if ($confirm) {
            return $this->withConfirm->get($shipment, $currentShipmentNumber);
        }

        return $this->withoutConfirm->get($shipment, $currentShipmentNumber);
    }
}
