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

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\Convert\Order as ConvertOrder;
use TIG\PostNL\Service\Shipment\CreateShipment;

class CreateShipments extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var Order
     */
    private $currentOrder;

    /**
     * @var CreateShipment
     */
    private $createShipment;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context                $context
     * @param Filter                 $filter
     * @param OrderCollectionFactory $collectionFactory
     * @param ConvertOrder           $convertOrder
     * @param CreateShipment $createShipment
     */
    public function __construct(
        Context $context,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        ConvertOrder $convertOrder,
        CreateShipment $createShipment
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->convertOrder = $convertOrder;
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
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /** @var Order $order */
        foreach ($collection as $order) {
            $this->createShipment->create($order);
        }

        $this->handleErrors();

        return $this->redirectBack();
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

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectBack()
    {
        $redirectPath = 'sales/shipment/index';
        if (!empty($this->errors)) {
            $redirectPath = 'sales/order/index';
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }
}
