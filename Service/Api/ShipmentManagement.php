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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Api\ShipmentManagementInterface;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Service\Api\ShipmentManagement\Confirm;
use TIG\PostNL\Service\Api\ShipmentManagement\CreateShipment;
use TIG\PostNL\Service\Api\ShipmentManagement\GenerateLabel;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class ShipmentManagement implements ShipmentManagementInterface
{
    /** @var $confirm */
    private $confirm;

    /** @var ResetPostNLShipment */
    private $resetPostNLShipment;

    /** @var GenerateLabel */
    private $generateLabel;

    /** @var CreateShipment */
    private $createShipment;

    /** @var Log */
    private $logger;

    /**
     * @param Confirm             $confirm
     * @param ResetPostNLShipment $resetPostNLShipment
     * @param GenerateLabel       $generateLabel
     * @param CreateShipment      $createShipment
     * @param Log                 $logger
     */
    public function __construct(
        Confirm $confirm,
        ResetPostNLShipment $resetPostNLShipment,
        GenerateLabel $generateLabel,
        CreateShipment $createShipment,
        Log $logger
    ) {
        $this->confirm = $confirm;
        $this->resetPostNLShipment = $resetPostNLShipment;
        $this->generateLabel = $generateLabel;
        $this->createShipment = $createShipment;
        $this->logger = $logger;
    }

    /**
     * @param int $shipmentId
     *
     * @return string
     */
    public function confirm($shipmentId)
    {
        try {
            $this->confirm->confirm($shipmentId);
            $message = __('Shipment #' . $shipmentId . ' Has been successfully confirmed');
        } catch (LocalizedException $exception) {
            // @codingStandardsIgnoreLine
            $message = __('Could not confirm shipment #' . $shipmentId);

            $this->logger->notice($message);
            $this->logger->notice($exception->getMessage());
        }

        return $message->render();
    }

    /**
     * @param int $shipmentId
     *
     * @return string
     */
    public function cancelConfirm($shipmentId)
    {
        try {
            $this->resetPostNLShipment->resetShipment($shipmentId);
            $message = __('Confirmation for shipment #' . $shipmentId . ' has been successfully canceled');
        } catch (CouldNotDeleteException $exception) {
            // @codingStandardsIgnoreLine
            $message = __('Could not cancel confirmation of shipment #' . $shipmentId);

            $this->logger->notice($message);
            $this->logger->notice($exception->getMessage());
        } catch (CouldNotSaveException $exception) {
            // @codingStandardsIgnoreLine
            $message = __('Could not cancel confirmation of shipment #' . $shipmentId);

            $this->logger->notice($message);
            $this->logger->notice($exception->getMessage());
        }

        return $message->render();
    }

    /**
     * @param int $shipmentId
     *
     * @api
     * @return string
     */
    public function generateLabel($shipmentId, $smartReturns)
    {
        try {
            $result = $this->generateLabel->generate($shipmentId, $smartReturns);
        } catch (LocalizedException $exception) {
            // @codingStandardsIgnoreLine
            $this->logger->notice(__('Could not generate label for shipment #' . $shipmentId));
            $this->logger->notice($exception->getMessage());
            $result = false;
        }

        // @codingStandardsIgnoreLine
        $message = __('Label successfully generated for shipment #' . $shipmentId);

        if (!$result) {
            $message = __('Could not generate label for shipment #' . $shipmentId);
        }

        return $message->render();
    }

    /**
     * @param int      $shipmentId
     * @param int|null $productCode
     * @param int|null $colliAmount
     *
     * @api
     * @return string
     */
    public function createShipment($shipmentId, $productCode = null, $colliAmount = null)
    {
        $result = $this->createShipment->create($shipmentId, $productCode, $colliAmount);

        $message = __('PostNL shipment successfully created for shipment #' . $shipmentId);

        if (!$result) {
            $message = __('Could not create a PostNL shipment for shipment #' . $shipmentId);
        }

        return $message->render();
    }
}
