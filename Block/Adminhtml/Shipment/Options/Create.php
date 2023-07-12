<?php

namespace TIG\PostNL\Block\Adminhtml\Shipment\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use TIG\PostNL\Api\OrderRepositoryInterface as PostNLOrderRepository;
use TIG\PostNL\Block\Adminhtml\Shipment\OptionsAbstract;
use TIG\PostNL\Service\Options\ShipmentSupported;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionSource;
use TIG\PostNL\Service\Shipment\Multicolli;
use \TIG\PostNL\Service\Parcel\Shipment\Count as ParcelCount;

class Create extends OptionsAbstract
{
    /**
     * @var PostNLOrderRepository
     */
    private $postnlOrderRepository;

    /**
     * @var null|int
     */
    private $productCode = null;

    /**
     * @var Multicolli
     */
    private $isMulticolliAllowed;

    /**
     * @var ParcelCount
     */
    private $parcelCount;

    /**
     * @param Context               $context
     * @param ShipmentSupported     $productOptions
     * @param ProductOptionSource   $productOptionsSource
     * @param OrderRepository       $orderRepository
     * @param PostNLOrderRepository $postnlOrderRepository
     * @param Multicolli            $isMulticolliAllowed
     * @param ParcelCount           $parcelCount
     * @param Registry              $registry
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        ShipmentSupported $productOptions,
        ProductOptionSource $productOptionsSource,
        OrderRepository $orderRepository,
        PostNLOrderRepository $postnlOrderRepository,
        Multicolli $isMulticolliAllowed,
        ParcelCount $parcelCount,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productOptions,
            $productOptionsSource,
            $orderRepository,
            $registry,
            $data
        );

        $this->isMulticolliAllowed = $isMulticolliAllowed;
        $this->postnlOrderRepository = $postnlOrderRepository;
        $this->parcelCount = $parcelCount;
    }

    /**
     * @return mixed
     */
    public function getProductCode()
    {
        if ($this->productCode === null) {
            $postnlOrder = $this->postnlOrderRepository->getByOrderId($this->getOrder()->getId());
            $this->productCode = $postnlOrder->getProductCode();
        }

        return $this->productCode;
    }

    /**
     * @return bool
     */
    public function isMultiColliAllowed()
    {
        $address = $this->getOrder()->getShippingAddress();

        return $this->isMulticolliAllowed->get($address->getCountryId());
    }

    /**
     * @return int|\Magento\Framework\Api\AttributeInterface|null
     */
    public function getParcelCount()
    {
        $postnlOrder = $this->postnlOrderRepository->getByOrderId($this->getOrder()->getId());
        if ($postnlOrder->getParcelCount()) {
            return $postnlOrder->getParcelCount();
        }

        return $this->parcelCount->get($this->getShipment());
    }
}
