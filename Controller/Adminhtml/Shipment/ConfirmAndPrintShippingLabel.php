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
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;

use TIG\PostNL\Helper\Labelling\GetLabels;
use TIG\PostNL\Helper\Labelling\SaveLabels;
use TIG\PostNL\Helper\Pdf\Get as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;

class ConfirmAndPrintShippingLabel extends Action
{
    /**
     * @var GetLabels
     */
    private $getLabels;

    /**
     * @var SaveLabels
     */
    private $saveLabels;

    /**
     * @var GetPdf
     */
    private $getPdf;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var Track
     */
    private $track;

    /**
     * @param Context            $context
     * @param GetLabels          $getLabels
     * @param SaveLabels         $saveLabels
     * @param GetPdf             $getPdf
     * @param ShipmentRepository $shipmentRepository
     * @param Track              $track
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        SaveLabels $saveLabels,
        GetPdf $getPdf,
        ShipmentRepository $shipmentRepository,
        Track $track
    ) {
        parent::__construct($context);
        $this->getLabels          = $getLabels;
        $this->saveLabels         = $saveLabels;
        $this->getPdf             = $getPdf;
        $this->shipmentRepository = $shipmentRepository;
        $this->track              = $track;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $shipment = $this->getShipment();

        if (!$shipment->getTracks()) {
            $this->track->set($shipment);
        }

        $labels     = $this->getLabels->get($shipment->getId());
        $labelModel = $this->saveLabels->save($labels);

        return $this->getPdf->get($labelModel);
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
