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
namespace TIG\PostNL\Helper\Labelling;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Model\ResourceModel\Shipment\CollectionFactory;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class GetLabels
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Labelling
     */
    private $labelling;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
    /**
     * @var Validator
     */
    private $labelValidator;

    /**
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param Validator                   $labelValidator
     * @param Labelling                   $labelling
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        Validator $labelValidator,
        Labelling $labelling
    ) {
        $this->labelling          = $labelling;
        $this->shipmentRepository = $shipmentRepository;
        $this->labelValidator     = $labelValidator;
    }

    /**
     * @param int|string $shipmentId
     *
     * @return array
     */
    public function get($shipmentId)
    {
        $labels = [];

        $shipment = $this->shipmentRepository->getByFieldWithValue('shipment_id', $shipmentId);

        if (!$shipment) {
            return $labels;
        }

        $labels[] = [
            'shipment' => $shipment,
            'labels' => $this->getLabel($shipment),
        ];

        $labels = $this->labelValidator->validate($labels);

        return $labels;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return array
     */
    private function getLabel(ShipmentInterface $shipment)
    {
        $labels = [];
        $parcelCount = $shipment->getParcelCount();
        for ($number = 1; $number <= $parcelCount; $number++) {
            $labels[] = $this->generateLabel($shipment, $parcelCount, $number);
        }

        return $labels;
    }

    /**
     * @param \TIG\PostNL\Model\Shipment $postnlShipment
     * @param                            $currentShipmentNumber
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function generateLabel($postnlShipment, $currentShipmentNumber)
    {
        $this->labelling->setParameters($postnlShipment, $currentShipmentNumber);
        $response = $this->labelling->call();
        $responseShipments = null;

        if (isset($response->ResponseShipments)) {
            $responseShipments = $response->ResponseShipments;
        }

        if (!is_object($response) || !isset($responseShipments->ResponseShipment)) {
            return __('Invalid generateLabel response: %1', var_export($response, true));
        }

        /**
         * @codingStandardsIgnoreLine
         * TODO: GenerateLabel call usually returns one label, but update so multiple labels are taking in account
         */
        return $responseShipments->ResponseShipment[0]->Labels->Label[0]->Content;
    }
}
