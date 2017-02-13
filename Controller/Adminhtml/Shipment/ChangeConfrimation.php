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
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

use Magento\Framework\Api\SearchCriteriaBuilder;

use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;

use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;

use TIG\PostNL\Model\ShipmentLabelRepository;
use TIG\PostNL\Model\ShipmentLabelInterface;

use TIG\PostNL\Model\ShipmentBarcodeRepository;
use TIG\PostNL\Model\ShipmentBarcodeInterface;

/**
 * Class ChangeConfrimation
 *
 * @package TIG\PostNL\Controller\Adminhtml\Shipment
 */
class ChangeConfrimation extends Action
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var PostNLShipmentRepository
     */
    private $postNLShipmentRepository;

    /**
     * @var ShipmentLabelRepository
     */
    private $shipmentLabelRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ShipmentBarcodeRepository
     */
    private $shipmentBarcodeRepository;

    /**
     * @param Context                   $context
     * @param ShipmentRepository        $shipmentRepository
     * @param PostNLShipmentRepository  $postNLShipmentRepository
     * @param ShipmentLabelRepository   $shipmentLabelRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param ShipmentBarcodeRepository $shipmentBarcodeRepository
     */
    public function __construct(
        Context $context,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentLabelRepository $shipmentLabelRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentBarcodeRepository $shipmentBarcodeRepository
    ) {
        parent::__construct($context);

        $this->shipmentRepository        = $shipmentRepository;
        $this->shipmentLabelRepository   = $shipmentLabelRepository;
        $this->postNLShipmentRepository  = $postNLShipmentRepository;
        $this->searchCriteriaBuilder     = $searchCriteriaBuilder;
        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
    }

    public function execute()
    {
        $this->resetConfirmedAt();
        $this->deleteBarcodes();
        $this->deleteLabels();

        // @todo Delete al the shipments associated tracks
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function resetConfirmedAt()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();
        $postNLShipment->setConfirmedAt(null);
        $this->postNLShipmentRepository->save($postNLShipment);
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    private function deleteBarcodes()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $this->getPostNLShipment()->getId());
        $barcodes = $this->shipmentBarcodeRepository->getList($searchCriteria->create());

        /** @var ShipmentBarcodeInterface $barcode */
        foreach ($barcodes->getItems() as $barcode) {
            $this->shipmentBarcodeRepository->delete($barcode);
        }
    }

    /**
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    private function deleteLabels()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $this->getPostNLShipment()->getId());
        $labels = $this->shipmentLabelRepository->getList($searchCriteria->create());

        /** @var ShipmentLabelInterface $label */
        foreach ($labels->getItems() as $label) {
            $this->shipmentLabelRepository->delete($label);
        }
    }

    /**
     * Retrieve postnl shipment model instance
     *
     * @return PostNLShipment
     */
    private function getPostNLShipment()
    {
        $shipmentId = $this->getRequest()->getParam('postnl_shipment_id');
        return $this->postNLShipmentRepository->getById($shipmentId);
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    private function getShipment()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        return $this->shipmentRepository->get($shipmentId);
    }
}