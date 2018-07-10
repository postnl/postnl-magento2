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
namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

abstract class ToolbarAbstract extends Action
{
    const PARCELCOUNT_PARAM_KEY = 'change_parcel';
    const PRODUCTCODE_PARAM_KEY = 'change_product';

    /**
     * @var Filter
     */
    //@codingStandardsIgnoreLine
    protected $uiFilter;

    /**
     * @var ShipmentRepositoryInterface
     */
    //@codingStandardsIgnoreLine
    protected $shipmentRepository;

    /**
     * @var OrderRepositoryInterface
     */
    //@codingStandardsIgnoreLine
    protected $orderRepository;

    /**
     * @var array
     */
    //@codingStandardsIgnoreLine
    protected $errors = [];

    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);

        $this->uiFilter = $filter;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Order $order
     * @param       $productCode
     */
    //@codingStandardsIgnoreLine
    protected function orderChangeProductCode(Order $order, $productCode)
    {
        $postnlOrder = $this->getPostNLOrder($order->getId());
        if (!$postnlOrder->getEntityId()) {
            $this->errors[] = __('Can not change product for non PostNL order %1', $order->getIncrementId());
            return;
        }

        $shipments = $order->getShipmentsCollection();
        foreach ($shipments as $shipment) {
            $this->shipmentChangeProductCode($shipment->getId(), $productCode);
        }

        $postnlOrder->setProductCode($productCode);
        $this->orderRepository->save($postnlOrder);
    }

    /**
     * @param $shipmentId
     * @param $productCode
     *
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function shipmentChangeProductCode($shipmentId, $productCode)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment->getId()) {
            return false;
        }

        if ($shipment->getConfirmedAt()) {
            $this->errors[] = __('Can not change product for confirmed shipment %1', $shipment->getShipmentId());
            return false;
        }

        $shipment->setProductCode($productCode);
        $this->shipmentRepository->save($shipment);
        return true;
    }

    /**
     * @param Order $order
     * @param       $parcelCount
     */
    //@codingStandardsIgnoreLine
    protected function orderChangeParcelCount(Order $order, $parcelCount)
    {
        $postnlOrder = $this->getPostNLOrder($order->getId());
        if (!$postnlOrder->getEntityId()) {
            $this->errors[] = __('Can not change parcel count for non PostNL order %1', $order->getIncrementId());
            return;
        }

        $shipments = $order->getShipmentsCollection();
        foreach ($shipments as $shipment) {
            $this->shipmentChangeParcelCount($shipment->getId(), $parcelCount);
        }

        $postnlOrder->setParcelCount($parcelCount);
        $this->orderRepository->save($postnlOrder);
    }

    /**
     * @param $shipmentId
     * @param $parcelCount
     *
     * @return bool
     */
    //@codingStandardsIgnoreLine
    protected function shipmentChangeParcelCount($shipmentId, $parcelCount)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment->getId()) {
            return false;
        }

        if (!$shipment->canChangeParcelCount()) {
            $this->errors[] = __('Can not change the parcel count for shipment %1', $shipment->getShipmentId());
            return false;
        }

        $shipment->setParcelCount($parcelCount);
        $this->shipmentRepository->save($shipment);
        return true;
    }

    /**
     * @return $this
     */
    //@codingStandardsIgnoreLine
    protected function handelErrors()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addWarningMessage($error);
        }

        return $this;
    }

    /**
     * @param $count
     *
     * @return mixed
     */
    //@codingStandardsIgnoreLine
    protected function getTotalCount($count)
    {
        $totalErrors = count($this->errors);
        return $count - $totalErrors;
    }

    /**
     * @param $orderId
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLOrder($orderId)
    {
        $postnlOrder = $this->orderRepository->getByOrderId($orderId);
        if (!$postnlOrder->getEntityId()) {
            $this->errors[] = __('Could not find a PostNL order for %1', $postnlOrder->getOrderId());
        }

        return $postnlOrder;
    }
}
