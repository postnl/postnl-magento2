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

use \Magento\Shipping\Block\Adminhtml\View as MagentoView;

/**
 * Class View
 *
 * @package TIG\PostNL\Block\Adminhtml\Shipment
 */
class View extends MagentoView
{
    /**
     * @param Context  $context
     * @param Registry $registry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
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
        $this->setPostNLPrintLabel();
    }

    private function setPostNLPrintLabel()
    {
        $this->buttonList->add(
            'test',
            [
                'label' => __('PostNL - Confirm shipment and print'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' .$this->getLabelUrl() .'\')'
            ]
        );
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
}
