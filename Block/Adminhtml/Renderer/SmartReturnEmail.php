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

use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use TIG\PostNL\Config\Provider\Webshop;

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
     * @param $shipmentId
     *
     * @return null|string
     */
    public function render($shipmentId)
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment) {
            return '';
        }
        // return a bool based on Smart return email sent

        $output = $shipment->getSmartReturnEmailSent();
        if (!isset($output)){
            return '';
        }

        if ($output) {

            return __('Email has been sent');
        }

        return __('Email has not been sent');

    }

}
