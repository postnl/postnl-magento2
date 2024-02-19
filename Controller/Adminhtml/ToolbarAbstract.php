<?php

namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Shipment\ProductOptions as ShipmentProductOptions;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use Magento\Sales\Model\Order;

//@codingStandardsIgnoreFile
abstract class ToolbarAbstract extends Action
{
    const PARCELCOUNT_PARAM_KEY = 'change_parcel';
    const PRODUCTCODE_PARAM_KEY = 'change_product';
    const PRODUCT_TIMEOPTION    = 'time';
    const PRODUCT_INSUREDTIER   = 'insuredtier';

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
     * @var ShipmentProductOptions
     */
    //@codingStandardsIgnoreLine
    protected $productOptions;

    /**
     * @var ResetPostNLShipment
     */
    //@codingStandardsIgnoreLine
    protected $resetService;

    /**
     * @var array
     */
    //@codingStandardsIgnoreLine
    protected $errors = [];

    /**
     * @var ProductOptions
     */
    private $options;

    /**
     * ToolbarAbstract constructor.
     *
     * @param Context                     $context
     * @param Filter                      $filter
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param OrderRepositoryInterface    $orderRepository
     * @param ShipmentProductOptions      $productOptions
     * @param ResetPostNLShipment         $resetPostNLShipment
     * @param ProductOptions              $options
     */
    public function __construct(
        Context                     $context,
        Filter                      $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface    $orderRepository,
        ShipmentProductOptions      $productOptions,
        ResetPostNLShipment         $resetPostNLShipment,
        ProductOptions              $options
    ) {
        parent::__construct($context);

        $this->uiFilter = $filter;
        $this->shipmentRepository = $shipmentRepository;
        $this->orderRepository = $orderRepository;
        $this->productOptions = $productOptions;
        $this->resetService = $resetPostNLShipment;
        $this->options = $options;
    }

    /**
     * @param Order $order
     * @param       $productCode
     * @param       $timeOption
     * @param       $insuredTier
     */
    //@codingStandardsIgnoreLine
    protected function orderChangeProductCode(Order $order, $productCode, $timeOption = null, $insuredTier = null)
    {
        $postnlOrder = $this->getPostNLOrder($order->getId());
        if (!$postnlOrder) {
            $this->errors[] = __('Can not change product for non PostNL order %1', $order->getIncrementId());
            return;
        }

        $acSettings = $this->getAcSettings($timeOption, $productCode);
        $shipments  = $order->getShipmentsCollection();
        $noError    = true;

        if ($shipments->getSize() > 0) {
            $noError = $this->shipmentsChangeProductCode($shipments, $productCode, $acSettings, $insuredTier);
        }

        if ($noError) {
            $this->setType($postnlOrder, $productCode);

            $postnlOrder->setProductCode($productCode);
            $postnlOrder->setInsuredTier($insuredTier);
            $postnlOrder->setAcInformation($acSettings);
            $this->orderRepository->save($postnlOrder);
        }
    }

    /**
     * @param $time
     * @param $productCode
     *
     * @return array
     */
    private function getAcSettings($time, $productCode)
    {
        $type = $time;
        if ($this->options->doesProductMatchFlags($productCode, 'group', 'eu_options')) {
            $type = strtolower(ProductInfo::SHIPMENT_TYPE_EPS);
        }

        if ($this->options->doesProductMatchFlags($productCode, 'group', 'global_options')) {
            $type = strtolower(ProductInfo::SHIPMENT_TYPE_GP);
        }

        if ($type && strlen($productCode) > 4) {
            $type .= '-' . substr($productCode, 0, 1);
        }

        $settings = $this->productOptions->getByType($type);
        if (!$settings) {
            return [];
        }

        return $settings;
    }

    /**
     * @param $shipments
     * @param $productCode
     * @param $acSettings
     * @param $insuredTier
     *
     * @return bool
     */
    private function shipmentsChangeProductCode($shipments, $productCode, $acSettings = null, $insuredTier = null)
    {
        $error = false;
        foreach ($shipments as $shipment) {
            $error = $this->shipmentChangeProductCode($shipment->getId(), $productCode, $acSettings, $insuredTier);
        }

        return $error;
    }

    /**
     * @param $shipmentId
     * @param $productCode
     * @param $acSettings
     * @param $insuredTier
     *
     * @return bool
     */
    private function shipmentChangeProductCode($shipmentId, $productCode, $acSettings, $insuredTier)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment->getId()) {
            return false;
        }

        if ($shipment->getMainBarcode()) {
            $shipment = $this->resetService->resetShipment($shipmentId);
        }

        $this->setType($shipment, $productCode);

        $shipment->setProductCode($productCode);
        $shipment->setInsuredTier($insuredTier);
        $shipment->setAcInformation($acSettings);
        $this->shipmentRepository->save($shipment);
        return true;
    }

    /**
     * If the merchant wants to switch the product code to Global or EU, we have to change the shipment type.
     * We're not implementing this for Domestic (yet), because this could give various options that we can't determine
     * with just the product code (e.g. Sunday, Evening, Daytime).
     *
     * @param $model
     * @param $productCode
     */
    private function setType($model, $productCode)
    {
        $method = 'setType';
        if ($model instanceof Shipment) {
            $method = 'setShipmentType';
        }

        // The product type is used to determine if specific information should be added, e.g. customs information
        if ($this->options->doesProductMatchFlags($productCode, 'group', 'global_options')) {
            $model->$method(ProductInfo::SHIPMENT_TYPE_GP);
        }

        if ($this->options->doesProductMatchFlags($productCode, 'group', 'eu_options')) {
            $model->$method(ProductInfo::SHIPMENT_TYPE_EPS);
        }
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

        if ($shipment->getMainBarcode()) {
            $this->resetService->resetShipment($shipmentId);
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
