<?php

namespace TIG\PostNL\Block\Adminhtml\Shipment;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Shipping\Block\Adminhtml\View as MagentoView;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Config\Validator\ValidAddress;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

// @codingStandardsIgnoreFile
class View extends MagentoView
{
    private PostNLShipmentRepository $postNLShipmentRepository;
    private ValidAddress $validAddress;
    private ReturnOptions $returnOptions;

    public function __construct(
        Context $context,
        Registry $registry,
        PostNLShipmentRepository $shipmentRepository,
        ValidAddress $validAddress,
        ReturnOptions $returnOptions,
        array $data = []
    ) {
        $this->postNLShipmentRepository = $shipmentRepository;
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

        $countryId = $this->getShipment()->getShippingAddress()->getCountryId();

        if ($countryId === 'NL' && $this->returnOptions->isSmartReturnActive()) {
            $this->setPostNLSendSmartReturnButton();
        }
        if ($countryId === 'BE') {
            $this->setPostNLSingleLabelReturnButton();
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

    private function setPostNLSingleLabelReturnButton(): void
    {
        $this->buttonList->add(
            'postnl_send_single_label_return',
            [
                'label' => __('PostNL - Generate return label'),
                'class' => 'save primary',
                'onclick' => 'download(\'' . $this->getBothShipmentUrl('GetSingleBeReturnLabel') . '\')',
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
        return $this->getBothShipmentUrl('CancelConfirmation');
    }

    private function getSendSmartReturnUrl()
    {
        return $this->getBothShipmentUrl('GetSmartReturnLabel');
    }

    private function getBothShipmentUrl(string $urlKey): string
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();

        return $this->getUrl(
            'postnl/shipment/' . $urlKey,
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
