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
namespace TIG\PostNL\Block\Adminhtml\Shipment;

use \Magento\Backend\Block\Widget\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\AbstractExtensibleObject;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use \TIG\PostNL\Model\Shipment as PostNLShipment;

use \Magento\Shipping\Block\Adminhtml\View as MagentoView;

/**
 * Class View
 *
 * @package TIG\PostNL\Block\Adminhtml\Shipment
 */
class View extends MagentoView
{
    /**
     * @var string
     */
    private $printRoute = 'postnl/shipment/confirmAndPrintShippingLabel';

    /**
     * @var string
     */
    private $printLabel = 'PostNL - Confirm And Print Shipment Label';

    /**
     * @var PostNLShipmentRepository
     */
    private $postNLShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Context                   $context
     * @param Registry                  $registry
     * @param PostNLShipmentRepository  $shipmentRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostNLShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->postNLShipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;

        parent::__construct($context, $registry, $data);
    }

    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();

        $order  = $this->getShipment()->getOrder();
        $method = $order->getShippingMethod();

        if ($method !== 'tig_postnl_regular') {
            return;
        }

        $this->buttonList->remove('print');
        $this->buttonList->update('save', 'label', __('Send Shipment Email'));
        $this->setPostNLPrintLabelButtonData();
        $this->setPostNLPrintLabelButton();
    }

    private function setPostNLPrintLabelButton()
    {
        $this->buttonList->add(
            'postnl_print',
            [
                'label' => __($this->printLabel),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' .$this->getLabelUrl() .'\')'
            ]
        );
    }

    private function setPostNLChangeConfirmButton()
    {
        $this->buttonList->add(
            'postnl_change_confirm',
            [
                'label'   => __('PostNL - Change Confirmation'),
                'class'   => 'delete primary',
                'style'   => 'background-color: #ea2102;',
                'onclick' =>
                    'deleteConfirm(\'' . __(
                        'Are you sure that you wish to reset the confirmation status of this shipment? You will need to '
                        . 'confirm this shipment with PostNL again before you can send it. This action will remove all barcodes'
                        . ' and labels associated with this shipment. You can not undo this action.'
                    ) . '\', \'' . $this->getAlterUrl() . '\')'
            ]
        );
    }

    private function setPostNLPrintLabelButtonData()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();
        if (!empty($postNLShipment->getConfirmedAt())) {
            $this->printLabel = 'PostNL - Print label';
            $this->setPostNLChangeConfirmButton();
        }
    }

    /**
     * @return string
     */
    private function getLabelUrl()
    {
        return $this->getUrl($this->printRoute, ['shipment_id' => $this->getShipment()->getId()]);
    }

    /**
     * @return string
     */
    private function getAlterUrl()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();

        return $this->getUrl(
            'postnl/shipment/ChangeConfrimation',
            [
                'postnl_shipment_id' => $postNLShipment->getId(),
                'shipment_id'        => $this->getShipment()->getId()
            ]
        );
    }

    /**
     * @return AbstractExtensibleObject
     */
    private function getPostNLShipment()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $this->getShipment()->getId());
        $searchCriteria->setPageSize(1);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems()[0];
    }
}
