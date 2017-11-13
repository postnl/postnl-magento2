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
use TIG\PostNL\Block\Adminhtml\Shipment\OptionsAbstract;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionSource;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Block\Adminhtml\Renderer\ShipmentType;

class View extends OptionsAbstract
{
    /**
     * @var PostNLShipmentRepository
     */
    private $postNLShipmentRepository;

    /**
     * @var ShipmentType
     */
    private $productCodeRenderer;

    /**
     * @param Context                  $context
     * @param ProductOptions           $productOptions
     * @param ProductOptionSource      $productOptionsSource
     * @param OrderRepository          $orderRepository
     * @param Registry                 $registry
     * @param PostNLShipmentRepository $shipmentRepository
     * @param ShipmentType             $shipmentType
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        ProductOptions $productOptions,
        ProductOptionSource $productOptionsSource,
        OrderRepository $orderRepository,
        Registry $registry,
        PostNLShipmentRepository $shipmentRepository,
        ShipmentType $shipmentType,
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

        $this->postNLShipmentRepository = $shipmentRepository;
        $this->productCodeRenderer      = $shipmentType;
    }

    /**
     * @return string
     */
    public function getProductOptionValue()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();
        return $this->productCodeRenderer->render($postNLShipment->getProductCode(), false);
    }

    /**
     * @return null|\TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function getPostNLShipment()
    {
        return $this->postNLShipmentRepository->getByFieldWithValue('shipment_id', $this->getShipment()->getId());
    }
}
