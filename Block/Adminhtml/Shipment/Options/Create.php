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
namespace TIG\PostNL\Block\Adminhtml\Shipment\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use TIG\PostNL\Api\OrderRepositoryInterface as PostNLOrderRepository;
use TIG\PostNL\Block\Adminhtml\Shipment\OptionsAbstract;
use TIG\PostNL\Config\Provider\ProductOptions;
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
     * @param ProductOptions        $productOptions
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
        ProductOptions $productOptions,
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
