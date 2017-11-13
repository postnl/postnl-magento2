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

use TIG\PostNL\Config\Validator\ValidAddress;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\Shipping\Block\Adminhtml\View as MagentoView;

// @codingStandardsIgnoreFile
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
     * @var ValidAddress
     */
    private $validAddress;

    /**
     * @param Context                  $context
     * @param Registry                 $registry
     * @param PostNLShipmentRepository $shipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param ValidAddress             $validAddress
     * @param array                    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PostNLShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ValidAddress $validAddress,
        array $data = []
    ) {
        $this->postNLShipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->validAddress = $validAddress;

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
        //@codingStandardsIgnoreLine
        $this->buttonList->update('save', 'label', __('Send Shipment Email'));
        $this->setPostNLPrintLabelButton();
        $this->setPostNLPrintLabelButtonData();
        $this->setPostNLPrintLabelWithoutConfirmButton();
    }

    /**
     * Add the PostNL print label button.
     */
    private function setPostNLPrintLabelButton()
    {
        $this->buttonList->add(
            'postnl_print',
            [
                // @codingStandardsIgnoreLine
                'label' => __($this->printLabel),
                'class' => 'save primary',
                'onclick' => 'download(\'' .$this->getLabelUrl() .'\')'
            ]
        );
    }

    /**
     * Add the PostNL change confirmation button.
     */
    private function setPostNLChangeConfirmButton()
    {
        /** @codingStandardsIgnoreStart */
        $this->buttonList->add(
            'postnl_change_confirm',
            [
                'label'   => __('PostNL - Change Confirmation'),
                'class'   => 'delete primary',
                'onclick' =>
                    'deleteConfirm(\'' . __(
                        'Are you sure that you wish to reset the confirmation status of this shipment?'
                        . ' You will need to confirm this shipment with PostNL again before you can send it.'
                        .' This action will remove all barcodes'
                        . ' and labels associated with this shipment. You can not undo this action.'
                    ) . '\', \'' . $this->getAlterUrl() . '\')'
            ]
        );
        /** @codingStandardsIgnoreEnd */
    }

    private function setPostNLPrintLabelWithoutConfirmButton()
    {
        $this->buttonList->add(
            'postnl_print_without_confirm',
            [
                // @codingStandardsIgnoreLine
                'label' => __('PostNL - Print Label'),
                'class' => 'save primary',
                'onclick' => 'download(\'' .$this->getLabelWithoutConfirmUrl() .'\')'
            ]
        );
    }

    private function setPostNLConfirmButton()
    {
        $this->buttonList->add(
            'postnl_confirm_shipment',
            [
                // @codingStandardsIgnoreLine
                'label' => __('PostNL - Confirm'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getConfirmUrl() . '\')',
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
            $this->setPostNLChangeConfirmButton();
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
        return $this->getUrl($this->printRoute, ['shipment_id' => $this->getShipment()->getId()]);
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
    private function getAlterUrl()
    {
        /** @var PostNLShipment $postNLShipment */
        $postNLShipment = $this->getPostNLShipment();

        return $this->getUrl(
            'postnl/shipment/ChangeConfirmation',
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
