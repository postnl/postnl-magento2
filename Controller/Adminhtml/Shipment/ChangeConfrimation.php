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

use TIG\PostNL\Services\Shipment\ShipmentService;
use TIG\PostNL\Services\Shipment\Track\DeleteTrack;
use TIG\PostNL\Services\Shipment\Label\DeleteLabel;
use TIG\PostNL\Services\Shipment\Barcode\DeleteBarcode;

use TIG\PostNL\Model\Shipment as PostNLShipment;

/**
 * Class ChangeConfrimation
 *
 * @package TIG\PostNL\Controller\Adminhtml\Shipment
 */
class ChangeConfrimation extends Action
{
    /**
     * @var ShipmentService
     */
    private $shipmentService;

    /**
     * @var DeleteLabel
     */
    private $labelDeleteHandler;

    /**
     * @var DeleteBarcode
     */
    private $barcodeDeleteHandler;

    /**
     * @var DeleteTrack
     */
    private $trackDeleteHandler;

    /**
     * @var int
     */
    private $postNLShipmentId;

    /**
     * @var int
     */
    private $shipmentId;

    /**
     * @param Context         $context
     * @param ShipmentService $shipmentService
     * @param DeleteLabel     $labelDeleteHandler
     * @param DeleteBarcode   $barcodeDeleteHandler
     * @param DeleteTrack     $trackDeleteHandler
     */
    public function __construct(
        Context $context,
        ShipmentService $shipmentService,
        DeleteLabel $labelDeleteHandler,
        DeleteBarcode $barcodeDeleteHandler,
        DeleteTrack $trackDeleteHandler
    ) {
        parent::__construct($context);

        $this->shipmentService      = $shipmentService;
        $this->barcodeDeleteHandler = $barcodeDeleteHandler;
        $this->labelDeleteHandler   = $labelDeleteHandler;
        $this->trackDeleteHandler   = $trackDeleteHandler;
    }

    /**
     * When you change the consignment confirmation,
     * all the associated elements in question will be removed from the shipment.
     * After that, new information like the shipping address can be set, before re-confirming the consignment.
     *
     * @return $this
     */
    public function execute()
    {
        $this->postNLShipmentId = $this->getRequest()->getParam('postnl_shipment_id');
        $this->shipmentId       = $this->getRequest()->getParam('shipment_id');

        $this->resetConfirmedAt();
        $this->barcodeDeleteHandler->deleteAllByShipmentId($this->postNLShipmentId);
        $this->labelDeleteHandler->deleteAllByParentId($this->postNLShipmentId);
        $this->trackDeleteHandler->deleteAllByShipmentId($this->shipmentId);

        $resultDirect = $this->resultRedirectFactory->create();
        return $resultDirect->setPath(
            'sales/shipment/view',
            ['shipment_id' => $this->shipmentId]
        );
    }

    /**
     * Resets the confirmation date to null.
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function resetConfirmedAt()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->shipmentService->getPostNLShipment($this->postNLShipmentId);
        $postNLShipment->setConfirmedAt(null);
        $this->shipmentService->save($postNLShipment);
    }
}
