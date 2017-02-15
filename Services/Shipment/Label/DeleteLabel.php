<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Services\Shipment\Label;

use TIG\PostNL\Model\ShipmentLabelRepository;
use TIG\PostNL\Model\ShipmentLabelInterface;

use TIG\PostNL\Services\Shipment\ShipmentServiceAbstract;

use Magento\Framework\Api\SearchCriteriaBuilder;

use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;

use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;

use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

/**
 * Class DeleteLabel
 *
 * @package TIG\PostNL\Services\Shipment\Label
 */
class DeleteLabel extends ShipmentServiceAbstract
{
    /**
     * @var ShipmentLabelRepository
     */
    private $shipmentLabelRepository;

    /**
     * @param ShipmentLabelRepository $shipmentLabelRepository
     * @param Log                       $log
     * @param PostNLShipmentRepository  $postNLShipmentRepository
     * @param ShipmentRepository        $shipmentRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentLabelRepository $shipmentLabelRepository,
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $log,
            $postNLShipmentRepository,
            $shipmentRepository,
            $searchCriteriaBuilder
        );

        $this->shipmentLabelRepository = $shipmentLabelRepository;
    }

    /**
     * Deletes one single label.
     *
     * @param ShipmentLabelInterface $label
     */
    public function delete($label)
    {
        try {
            $this->shipmentLabelRepository->delete($label);
        } catch (PostNLException $exception) {
            $this->logger->alert('Can\'t delete shipment label', $exception->getLogMessage());
        }
    }

    /**
     * Deletes all labels associated to the PostNL Shipment ID.
     *
     * @param $postNLShipmentId
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteAllByParentId($postNLShipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'parent_id',
            $postNLShipmentId
        );

        $labels = $this->shipmentLabelRepository->getList($searchCriteria->create());

        /** @var ShipmentLabelInterface $label */
        foreach ($labels->getItems() as $label) {
            // @codingStandardsIgnoreLine
            $this->delete($label);
        }
    }
}
