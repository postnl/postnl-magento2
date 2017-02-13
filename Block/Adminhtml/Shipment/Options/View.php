<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Shipment\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderRepository;
use TIG\PostNL\Block\Adminhtml\Shipment\OptionsAbstract;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionSource;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class View
 *
 * @package TIG\PostNL\Block\Adminhtml\Shipment\Options
 */
class View extends OptionsAbstract
{
    /**
     * @var PostNLShipmentRepository
     */
    private $postNLShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Context                  $context
     * @param ProductOptions           $productOptions
     * @param ProductOptionSource      $productOptionsSource
     * @param OrderRepository          $orderRepository
     * @param Registry                 $registry
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param PostNLShipmentRepository $shipmentRepository
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        ProductOptions $productOptions,
        ProductOptionSource $productOptionsSource,
        OrderRepository $orderRepository,
        Registry $registry,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PostNLShipmentRepository $shipmentRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productOptions,
            $productOptionsSource,
            $orderRepository,
            $registry,
            $searchCriteriaBuilder,
            $shipmentRepository,
            $data
        );

        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->postNLShipmentRepository = $shipmentRepository;
    }


    /**
     * @return mixed
     */
    public function getProductOption()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();
        return $this->productSource->getOptionsByCode($postNLShipment->getProductCode());
    }

    /**
     * @return AbstractExtensibleObject
     */
    public function getPostNLShipment()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $this->getShipment()->getId());
        $searchCriteria->setPageSize(1);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems()[0];
    }
}
