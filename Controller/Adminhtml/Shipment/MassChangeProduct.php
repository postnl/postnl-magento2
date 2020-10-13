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

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Controller\Adminhtml\ToolbarAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Service\Shipment\GuaranteedOptions;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class MassChangeProduct extends ToolbarAbstract
{
    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        ShipmentCollectionFactory $collectionFactory,
        GuaranteedOptions $guaranteedOptions,
        ResetPostNLShipment $resetPostNLShipment,
        ProductOptions $options
    ) {
        parent::__construct(
            $context,
            $filter,
            $shipmentRepository,
            $orderRepository,
            $guaranteedOptions,
            $resetPostNLShipment,
            $options
        );

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection     = $this->collectionFactory->create();
        $collection     = $this->uiFilter->getCollection($collection);
        $newParcelCount = $this->getRequest()->getParam(self::PRODUCTCODE_PARAM_KEY);
        $timeOption     = $this->getRequest()->getParam(self::PRODUCT_TIMEOPTION);

        $this->changeProductCode($collection, $newParcelCount, $timeOption);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/index');
        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @param $newParcelCount
     * @param $timeOption
     */
    private function changeProductCode($collection, $newParcelCount, $timeOption)
    {
        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->orderChangeProductCode($shipment->getOrder(), $newParcelCount, $timeOption);
        }

        $this->handelErrors();

        $count = $this->getTotalCount($collection->getSize());
        if ($count > 0) {
            $this->messageManager->addSuccessMessage(
                __('Productcode changed for %1 shipment(s)', $count)
            );
        }
    }
}
