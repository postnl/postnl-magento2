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

use Magento\Framework\Message\Manager as MessageManager;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Service\Shipment\ConfirmLabel;

/**
 * Class GetLabels
 *
 * Every property we use here is needed and shouldn't be moved for risk of over-
 * specifying. That's why we ignore this file.
 *
 * @codingStandardsIgnoreFile
 */
class GetLabels
{
    /** @var MessageManager $messageManager */
    private $messageManager;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var Validator */
    private $labelValidator;

    /** @var ShipmentLabelRepositoryInterface */
    private $shipmentLabelRepository;

    /** @var GenerateLabel */
    private $generateLabel;

    /** @var ConfirmLabel */
    private $confirmLabel;

    /**
     * GetLabels constructor.
     *
     * @param MessageManager                   $messageManager
     * @param ShipmentLabelRepositoryInterface $shipmentLabelRepository
     * @param ShipmentRepositoryInterface      $shipmentRepository
     * @param GenerateLabel                    $generateLabel
     * @param Validator                        $labelValidator
     * @param ConfirmLabel                     $confirmLabel
     */
    public function __construct(
        MessageManager $messageManager,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        GenerateLabel $generateLabel,
        Validator $labelValidator,
        ConfirmLabel $confirmLabel
    ) {
        $this->messageManager          = $messageManager;
        $this->shipmentRepository      = $shipmentRepository;
        $this->labelValidator          = $labelValidator;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->generateLabel           = $generateLabel;
        $this->confirmLabel            = $confirmLabel;
    }

    /**
     * @param      $shipmentId
     * @param bool $confirm
     * @param bool $smartReturn
     *
     * @return array|\Magento\Framework\Phrase|string|\TIG\PostNL\Api\Data\ShipmentLabelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($shipmentId, $confirm = true, $smartReturn = false)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        if (!$shipment) {
            return [];
        }

        $this->labelValidator->validateProduct($shipment);
        $labels = $this->getLabels($shipment, $confirm, $smartReturn);
        $labels = $this->labelValidator->validate($labels);

        $errors  = $this->labelValidator->getErrors();
        $notices = $this->labelValidator->getNotices();

        $this->handleRequestMessages($errors);
        $this->handleRequestMessages($notices, 'notice');

        return $labels;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $confirm
     * @param                   $smartReturn
     * @return \Magento\Framework\Phrase|string|\TIG\PostNL\Api\Data\ShipmentLabelInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getLabels(ShipmentInterface $shipment, $confirm, $smartReturn)
    {
        $labels = $this->shipmentLabelRepository->getByShipment($shipment);

        if ($smartReturn) {
            $labels = null;
        }

        if ($labels && $confirm) {
            $this->confirmLabel->confirm($shipment);

            return $labels;
        }

        if ($labels) {
            return $labels;
        }

        return $this->generateLabel->get($shipment, 1, $confirm);
    }

    /**
     * @param        $errors
     * @param string $type
     */
    public function handleRequestMessages($errors = [], $type = 'warning')
    {
        foreach ($errors as $error) {
            $type == 'warning' ? $this->messageManager->addWarningMessage($error)
                : $this->messageManager->addNoticeMessage($error);
        }
    }
}
