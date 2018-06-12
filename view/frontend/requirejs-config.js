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
var config = {
    map: {
        '*': {
            "Magento_Checkout/template/shipping-address/shipping-method-item.html" :
                "TIG_PostNL/template/shipping-address/shipping-method-item.html",
            //Magento Backwards Compatibility
            "Magento_Checkout/template/shipping.html" :
                "TIG_PostNL/template/shipping.html",
            //Rubic Clean Checkout Compatibility
            "Rubic_CleanCheckout/template/shipping-address/shipping-method-item.html":
                "TIG_PostNL/template/shipping-address/shipping-method-item.html",
            //Amasty Checkout Compatibility
            "Amasty_Checkout/template/onepage/shipping/methods.html" :
                "TIG_PostNL/template/Compatibility/amasty_checkout/methods.html",
            "Amasty_Checkout/js/view/shipping" :
                "TIG_PostNL/js/Compatibility/amasty_checkout/shipping"
        }
    }
};
