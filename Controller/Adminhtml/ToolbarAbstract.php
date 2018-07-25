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

//@codingStandardsIgnoreFile
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
        if (!$postnlOrder) {
            $this->errors[] = __('Can not change product for non PostNL order %1', $order->getIncrementId());
            return;
        }

        $shipments = $order->getShipmentsCollection();
        $noError     = true;

        if ($shipments->getSize() > 0) {
            $noError = $this->shipmentsChangeProductCode($shipments, $productCode);
        }

        if ($noError) {
            $postnlOrder->setProductCode($productCode);
            $this->orderRepository->save($postnlOrder);
        }
    }

    /**
     * @param $shipments
     * @param $productCode
     *
     * @return bool
     */
    private function shipmentsChangeProductCode($shipments, $productCode)
    {
        $error = false;
        foreach ($shipments as $shipment) {
            $error = $this->shipmentChangeProductCode($shipment->getId(), $productCode);
        }

        return $error;
    }

    /**
     * @param $shipmentId
     * @param $productCode
     *
     * @return bool
     */
    private function shipmentChangeProductCode($shipmentId, $productCode)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment->getId()) {
            return false;
        }

        if ($shipment->getConfirmedAt()) {
            $magentoShipment = $shipment->getShipment();
            $this->errors[]  = __('First change the confirmation of shipment %1 before changing the product code.', $magentoShipment->getIncrementId());
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
        if (!$postnlOrder) {
            $this->errors[] = __('Can not change parcel count for non PostNL order %1', $order->getIncrementId());
            return;
        }

        $shipments = $order->getShipmentsCollection();
        $noError     = true;

        if ($shipments->getSize() > 0) {
            $noError = $this->shipmentsChangeParcelCount($shipments, $parcelCount);
        }

        if ($noError) {
            $postnlOrder->setParcelCount($parcelCount);
            $this->orderRepository->save($postnlOrder);
        }
    }

    /**
     * @param $shipments
     * @param $parcelCount
     *
     * @return bool
     */
    private function shipmentsChangeParcelCount($shipments, $parcelCount)
    {
        $error = false;
        foreach ($shipments as $shipment) {
            $error = $this->shipmentChangeParcelCount($shipment->getId(), $parcelCount);
        }

        return $error;
    }

    /**
     * @param $shipmentId
     * @param $parcelCount
     *
     * @return bool
     */
    private function shipmentChangeParcelCount($shipmentId, $parcelCount)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment->getId()) {
            return false;
        }

        if (!$shipment->canChangeParcelCount()) {
            $magentoShipment = $shipment->getShipment();
            $this->errors[]  = __('Can not change the parcel count for shipment %1', $magentoShipment->getIncrementId());
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
    private function getPostNLOrder($orderId)
    {
        $postnlOrder = $this->orderRepository->getByOrderId($orderId);
        if (!$postnlOrder) {
            $this->errors[] = __('Could not find a PostNL order for %1', $orderId);
        }

        return $postnlOrder;
    }
}
