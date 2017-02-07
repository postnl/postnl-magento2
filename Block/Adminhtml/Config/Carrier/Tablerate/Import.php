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
namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate;

use \Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class TablerateExport
 *
 * @package TIG\PostNL\Block\Adminhtml\Config\Carrier
 */
class Import extends AbstractElement
{
    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        parent::_construct();
        $this->setType('file');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        $html .= '<input id="time_condition" type="hidden" name="' . $this->getName() . '" value="' . time() . '" />';

        $html .= <<<EndHTML
        <script>
        require(['prototype'], function(){
        Event.observe($('carriers_tig_postnl_condition_name'), 'change', checkConditionName.bind(this));
        function checkConditionName(event)
        {
            var conditionNameElement = Event.element(event);
            if (conditionNameElement && conditionNameElement.id) {
                $('time_condition').value = '_' + conditionNameElement.value + '/' + Math.random();
            }
        }
        });
        </script>
EndHTML;

        $html .= parent::getElementHtml();

        return $html;
    }
}
