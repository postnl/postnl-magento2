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
define([
    'Magento_Checkout/js/view/shipping',
    'TIG_PostNL/js/Helper/State'
], function (
    Shipping,
    State
) {
    return Shipping.extend({
        canUseDeliveryOption: function () {
            var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active == 1;
            var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
            var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active == '1';

            return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
        },

        isPostNLDeliveryMethod: function (method) {
            return method.carrier_code == 'tig_postnl';
        },

        canUsePostnlDeliveryOptions: function (method) {
            if (!this.canUseDeliveryOption()) {
                return false;
            }

            var result = this.isPostNLDeliveryMethod(method);

            if (result) {
                State.method(method);
            }

            return result;
        }
    });
});
