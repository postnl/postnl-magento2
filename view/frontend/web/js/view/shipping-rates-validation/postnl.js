/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../Models/shipping-rates-validator/postnl',
        '../../Models/shipping-rates-validation-rules/postnl'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        postnlShippingRatesValidator,
        postnlShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('tig_postnl', postnlShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('tig_postnl', postnlShippingRatesValidationRules);
        return Component;
    }
);
