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
namespace TIG\PostNL\Block\Adminhtml\Shipment;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Shipping\Block\Adminhtml\View as MagentoView;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Validator\ValidAddress;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

// @codingStandardsIgnoreFile
class View extends MagentoView
{
    /** @var \TIG\PostNL\Model\ShipmentRepository $postNLShipmentRepository */
    private $postNLShipmentRepository;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var \TIG\PostNL\Config\Validator\ValidAddress $validAddress */
    private $validAddress;

    /** @var ReturnOptions  */
    private $returnOptions;

    /**
     * @param Context                  $context
     * @param Registry                 $registry
     * @param PostNLShipmentRepository $shipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param ValidAddress             $validAddress
     * @param ReturnOptions            $returnOptions
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostNLShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ValidAddress $validAddress,
        ReturnOptions $returnOptions,
        array $data = []
    ) {
        $this->postNLShipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->validAddress             = $validAddress;
        $this->returnOptions            = $returnOptions;

        parent::__construct($context, $registry, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $order  = $this->getShipment()->getOrder();
        $method = $order->getShippingMethod();

        if ($method !== 'tig_postnl_regular') {
            return;
        }

        if (!$this->validAddress->check()) {
            return;
        }

        if (!$this->getPostNLShipment()) {
            return;
        }

        $this->processButtons();
    }

    /**
     * Remove, update and add buttons.
     */
    private function processButtons()
    {
        $this->buttonList->remove('print');
        $this->buttonList->update('save', 'label', __('Send Shipment Email'));
        $this->setPostNLPrintLabelButton();
        $this->setPostNLPrintLabelButtonData();
        $this->setPostNLPrintLabelWithoutConfirmButton();
        $this->setPostNLPrintPackingslipButton();

        if ($this->returnOptions->isSmartReturnActive()) {
            $this->setPostNLSendSmartReturnButton();
        }
    }

    /**
     * Add the PostNL print label button.
     */
    private function setPostNLPrintLabelButton()
    {
        $this->buttonList->add(
            'postnl_print',
            [
                'label' => __('PostNL - Confirm And Print Shipment Label'),
                'class' => 'save primary',
                'onclick' => 'download(\'' . $this->getLabelUrl() . '\')'
            ]
        );
    }

    /**
     * Add the PostNL cancel confirmation button.
     */
    private function setPostNLCancelConfirmButton()
    {
        $this->buttonList->add(
            'postnl_cancel_confirm',
            [
                'label'   => __('PostNL - Cancel Confirmation'),
                'class'   => 'delete primary',
                'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure that you wish to reset the confirmation status of this shipment?'
                        . ' You will need to confirm this shipment with PostNL again before you can send it.'
                        . ' This action will remove all barcodes'
                        . ' and labels associated with this shipment. You can not undo this action.'
                    ) . '\', \'' . $this->getCancelConfirmationUrl() . '\')'
            ]
        );
    }

    private function setPostNLPrintLabelWithoutConfirmButton()
    {
        $postNLShipment = $this->getPostNLShipment();
        $mainBarcode    = $postNLShipment->getMainBarcode();

        $this->buttonList->add(
            'postnl_print_without_confirm',
            [
                'label' => $mainBarcode ? __('PostNL - Print Label') : __('PostNL - Generate Label'),
                'class' => 'save primary',
                'onclick' => 'download(\'' . $this->getLabelWithoutConfirmUrl() . '\')'
            ]
        );
    }

    private function setPostNLPrintPackingslipButton()
    {
        $this->buttonList->add(
            'postnl_print_packingslip',
            [
                'label' => __('PostNL - Print Packingslip'),
                'class' => 'save primary',
                'onclick' => 'download(\'' . $this->getPackingslipUrl() . '\')'
            ]
        );
    }

    private function setPostNLConfirmButton()
    {
        $this->buttonList->add(
            'postnl_confirm_shipment',
            [
                'label' => __('PostNL - Confirm'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getConfirmUrl() . '\')',
            ]
        );
    }

    private function setPostNLSendSmartReturnButton()
    {
        $this->buttonList->add(
            'postnl_send_smart_return',
            [
                'label' => __('PostNL - Send Smart Return'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getSendSmartReturnUrl() . '\')',
            ]
        );
    }

    /**
     * Set the correct text.
     */
    private function setPostNLPrintLabelButtonData()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();
        $confirmedAt = $postNLShipment->getConfirmedAt();
        if (!empty($confirmedAt)) {
            $this->buttonList->remove('postnl_print');
            $this->setPostNLCancelConfirmButton();
        }

        if (empty($confirmedAt) && $postNLShipment->getMainBarcode()) {
            $this->setPostNLConfirmButton();
        }
    }

    /**
     * @return string
     */
    private function getLabelUrl()
    {
        return $this->getUrl(
            'postnl/shipment/confirmAndPrintShippingLabel',
            ['shipment_id' => $this->getShipment()->getId()]
        );
    }

    /**
     * @return string
     */
    private function getLabelWithoutConfirmUrl()
    {
        return $this->getUrl(
            'postnl/shipment/PrintShippingLabel',
            ['shipment_id' => $this->getShipment()->getId()]
        );
    }

    /**
     * @return string
     */
    private function getPackingslipUrl()
    {
        return $this->getUrl(
            'postnl/shipment/PrintPackingslip',
            ['shipment_id' => $this->getShipment()->getId()]
        );
    }

    /**
     * @return string
     */
    private function getConfirmUrl()
    {
        return $this->getUrl(
            'postnl/shipment/ConfirmShipping',
            ['shipment_id' => $this->getShipment()->getId()]
        );
    }

    /**
     * @return string
     */
    private function getCancelConfirmationUrl()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();

        return $this->getUrl(
            'postnl/shipment/CancelConfirmation',
            [
                'postnl_shipment_id' => $postNLShipment->getId(),
                'shipment_id'        => $this->getShipment()->getId(),
            ]
        );
    }

    private function getSendSmartReturnUrl()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();

        return $this->getUrl(
            'postnl/shipment/GetSmartReturnLabel',
            [
                'postnl_shipment_id' => $postNLShipment->getId(),
                'shipment_id'        => $this->getShipment()->getId(),
            ]
        );
    }

    /**
     * @return \TIG\PostNL\Api\Data\ShipmentInterface|null
     */
    private function getPostNLShipment()
    {
        return $this->postNLShipmentRepository->getByShipmentId($this->getShipment()->getId());
    }
}
