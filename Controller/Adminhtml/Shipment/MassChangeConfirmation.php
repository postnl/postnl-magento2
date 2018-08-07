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
namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class MassChangeConfirmation extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResetPostNLShipment
     */
    private $resetService;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param Context                     $context
     * @param Filter                      $filter
     * @param ShipmentCollectionFactory   $collectionFactory
     * @param ResetPostNLShipment         $resetService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        ResetPostNLShipment $resetService
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resetService = $resetService;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->resetService->resetShipment($shipment->getId());
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

        return $this;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectBack()
    {
        $redirectPath = 'sales/shipment/index';

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }
}
