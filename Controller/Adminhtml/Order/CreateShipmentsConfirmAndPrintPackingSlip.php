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
namespace TIG\PostNL\Controller\Adminhtml\Order;

use Magento\Framework\App\ResponseInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use TIG\PostNL\Controller\Adminhtml\LabelAbstract;
use TIG\PostNL\Controller\Adminhtml\PdfDownload as GetPdf;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\CreateShipment;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;

class CreateShipmentsConfirmAndPrintPackingSlip extends LabelAbstract
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CreateShipment
     */
    private $createShipment;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param Context                   $context
     * @param GetLabels                 $getLabels
     * @param GetPdf                    $getPdf
     * @param Filter                    $filter
     * @param OrderCollectionFactory    $collectionFactory
     * @param CreateShipment            $createShipment
     * @param Track                     $track
     * @param BarcodeHandler            $barcodeHandler
     * @param GetPackingslip            $getPackingSlip
     */
    public function __construct(
        Context $context,
        GetLabels $getLabels,
        GetPdf $getPdf,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        CreateShipment $createShipment,
        Track $track,
        BarcodeHandler $barcodeHandler,
        GetPackingslip $getPackingSlip
    ) {
        parent::__construct($context, $getLabels, $getPdf, $getPackingSlip, $barcodeHandler, $track);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->createShipment = $createShipment;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $packingSlips = $this->createShipmentsAndLoadPackingSlips();
        $this->handleErrors();

        return $packingSlips;
    }

    /**
     * Create or load the shipments and labels
     */
    private function createShipmentsAndLoadPackingSlips()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        foreach ($collection as $order) {
            $shipment = $this->createShipment->create($order);
            $address  = $shipment->getShippingAddress();
            $this->barcodeHandler->prepareShipment($shipment->getId(), $address->getCountryId());
            $this->setTracks($shipment);
            $this->setPackingslip($shipment->getId());
        }

        return $this->getPdf->get($this->labels, GetPdf::FILETYPE_PACKINGSLIP);
    }

    /**
     * @return $this
     */
    private function handleErrors()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        $shipmentErrors = $this->createShipment->getErrors();
        foreach ($shipmentErrors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        return $this;
    }
}
