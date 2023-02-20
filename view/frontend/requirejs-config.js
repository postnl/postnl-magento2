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
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'TIG_PostNL/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'TIG_PostNL/js/action/set-shipping-information-mixin': true
            },
            'Amasty_Checkout/js/action/set-shipping-information' : {
                'TIG_PostNL/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/set-billing-address': {
                'TIG_PostNL/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'TIG_PostNL/js/action/set-billing-address-mixin': true,
                'TIG_PostNL/js/action/reset-session-storage-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'TIG_PostNL/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'TIG_PostNL/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'TIG_PostNL/js/view/billing-address-mixin': true
            },
            'Onestepcheckout_Iosc/js/ajax': {
                'TIG_PostNL/js/view/onestepcheckout/ajax-mixin': true
            }
        }
    },
    map: {
        '*': {
            //Postcodecheck housenumber & addition in shipping and billing fields
            "Magento_Checkout/template/shipping-address/address-renderer/default.html" :
                "TIG_PostNL/template/shipping-address/address-renderer/default.html",
            "Magento_Checkout/template/shipping-information/address-renderer/default.html" :
                "TIG_PostNL/template/shipping-information/address-renderer/default.html",
            "Magento_Checkout/template/billing-address/details.html" :
                "TIG_PostNL/template/billing-address/details.html",
            'Amasty_CustomerAttributes/js/action/set-shipping-information-mixin' :
                'TIG_PostNL/js/action/set-shipping-information-mixin',
            //Amasty Checkout Compatibility
            "Amasty_Checkout/js/view/shipping-mixin" :
                "TIG_PostNL/js/Compatibility/amasty_checkout/shipping-mixin",
            //Mageplaza Checkout Compatibility
            "Mageplaza_Osc/template/container/address/shipping/address-renderer/default.html" :
                "TIG_PostNL/template/Compatibility/mageplaza_osc/shipping-address/address-renderer/default.html",
        }
    }
};
