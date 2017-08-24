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

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Service\Shipment\ConfirmLabel;

class GetLabels
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var Validator
     */
    private $labelValidator;

    /**
     * @var ShipmentLabelRepositoryInterface
     */
    private $shipmentLabelRepository;

    /**
     * @var GenerateLabel
     */
    private $generateLabel;

    /**
     * @var ConfirmLabel
     */
    private $confirmLabel;

    /**
     * @param ShipmentLabelRepositoryInterface $shipmentLabelRepository
     * @param ShipmentRepositoryInterface      $shipmentRepository
     * @param GenerateLabel                    $generateLabel
     * @param Validator                        $labelValidator
     * @param ConfirmLabel                     $confirmLabel
     */
    public function __construct(
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        GenerateLabel $generateLabel,
        Validator $labelValidator,
        ConfirmLabel $confirmLabel
    ) {
        $this->shipmentRepository      = $shipmentRepository;
        $this->labelValidator          = $labelValidator;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->generateLabel           = $generateLabel;
        $this->confirmLabel            = $confirmLabel;
    }

    /**
     * @param int|string $shipmentId
     * @param bool $confirm
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]
     */
    public function get($shipmentId, $confirm = true)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        if (!$shipment) {
            return [];
        }

        $labels = $this->getLabels($shipment, $confirm);
        $labels = $this->labelValidator->validate($labels);

        return $labels;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param bool $confirm
     *
     * @return array
     */
    private function getLabels(ShipmentInterface $shipment, $confirm)
    {
        $labels = [];
        $parcelCount = $shipment->getParcelCount();
        for ($number = 1; $number <= $parcelCount; $number++) {
            $labels[] = $this->getLabel($shipment, $number, $confirm);
        }

        return $labels;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $number
     * @param bool              $confirm
     *
     * @return \Magento\Framework\Phrase|string|\TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    private function getLabel(ShipmentInterface $shipment, $number, $confirm)
    {
        $label = $this->shipmentLabelRepository->getByShipment($shipment, $number);

        if ($label && $confirm) {
            $this->confirmLabel->confirm($shipment, $number);
            return $label;
        }

        if ($label) {
            return $label;
        }

        return $this->generateLabel->get($shipment, $number, $confirm);
    }
}
