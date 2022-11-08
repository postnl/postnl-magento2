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
namespace TIG\PostNL\Block\Adminhtml\Renderer;

use Magento\Framework\Phrase;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Api\Data\ShipmentInterface;

class SmartReturnEmail
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * DeepLink constructor.
     *
     * @param ShipmentRepository $shipmentRepository
     */
    public function __construct(
        ShipmentRepository $shipmentRepository
    ) {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param Shipment|string|int $shipment
     *
     * @return string
     */
    public function render($shipment)
    {
        if (!$shipment) {
            return '';
        }

        if ($shipment instanceof Shipment ) {
            $output = $shipment->getSmartReturnEmailSent();
        }else {
            /** @var ShipmentInterface $shipmentModel */
            $shipmentModel = $this->shipmentRepository->getByShipmentId($shipment);
            if (!$shipmentModel){
                return '';
            }
            $output = $shipmentModel->getSmartReturnEmailSent();
        }
        // return a bool based on Smart return email sent
        if (!isset($output)){
            return '';
        }
        if ($output) {
            return '&check;';
        }

        return '&#10539';
    }
}
