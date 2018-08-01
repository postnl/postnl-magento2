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
/* eslint-disable strict */
define(['jquery', 'mage/url'], function ($, url) {
    var productOptions = '';
    var defaultOption = '3085';

    return {

        setDefaultOption: function (option) {
            defaultOption = option;
        },

        getDefaultOption: function () {
            return defaultOption;
        },

        setProductOptions: function (options) {
            productOptions = options;
        },

        getProductOptions: function () {
            if (!productOptions) {
                return null;
            }

            return JSON.parse(productOptions);
        },

        getInputWarningMessage: function (option) {
            if (option === 'change_parcel') {
                return $.mage.__('Parcel count should be a number');
            }

            if (option === 'change_product') {
                return $.mage.__('Productcode should be a number and availble within the configuration');
            }
        },

        getSubmitUrl : function (option, grid) {
            var action = 'postnl/' + this.getCurrentGrid(grid) + '/' + this.getCurrentAction(option);
            return url.build(action);
        },

        /**
         * Gets the controller based on the currently selected action.
         *
         * @returns {*}
         */
        getCurrentAction : function (option) {
            if (option === 'change_parcel') {
                return 'MassChangeMulticolli';
            }

            if (option === 'change_product') {
                return 'MassChangeProduct';
            }
        },

        /**
         * Retuns the controller directory bases on the current grid.
         *
         * @returns {*}
         */
        getCurrentGrid : function (grid) {
            if (grid === 'sales_order_grid') {
                return 'order';
            }

            if (grid === 'sales_order_shipment_grid') {
                return 'shipment';
            }
        }
    };
});
