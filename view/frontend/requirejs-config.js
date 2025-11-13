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
            'TIG_PostNL/js/view/fillin-button': 'TIG_PostNL/js/view/fillin-button',
            'TIG_PostNL/js/cart/postnl-button': 'TIG_PostNL/js/cart/postnl-button'
        }
    }
};
