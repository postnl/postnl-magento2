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
namespace TIG\PostNL\Service\Shipment\Labelling\Generate;

use TIG\PostNL\Service\Shipment\Labelling\GenerateAbstract;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class WithConfirm extends GenerateAbstract
{
    public function __construct(
        Data $helper,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        Log $logger,
        Labelling $labelling
    ) {
        parent::__construct(
            $helper,
            $shipmentLabelFactory,
            $shipmentLabelRepository,
            $shipmentRepository,
            $logger
        );

        $this->labelService = $labelling;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentNumber
     *
     * @return null|\TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function get(ShipmentInterface $shipment, $currentNumber)
    {
        return $this->getLabel($shipment, $currentNumber, true);
    }
}
