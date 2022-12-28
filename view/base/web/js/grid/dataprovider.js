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
    var showGridToolbar = 1;
    var isGuaranteedActive = 0;
    var cargoOptions = '';
    var packageOptions = '';
    var insuredTierOptions = '';
    var defaultInsuredTier = '';
    var labelStartPositionOptions = '';

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

        setCargoTimeOptions: function (options) {
            cargoOptions = options;
        },

        getCargoTimeOptions: function () {
            if (!cargoOptions) {
                return null;
            }

            return JSON.parse(cargoOptions);
        },

        setPackagesTimeOptions: function (options) {
            packageOptions = options;
        },

        getPackagesTimeOptions: function () {
            if (!packageOptions) {
                return null;
            }

            return JSON.parse(packageOptions);
        },

        setInsuredTierOptions: function (options) {
            insuredTierOptions = options;
        },

        getInsuredTierOptions: function () {
            if (!insuredTierOptions) {
                return null;
            }

            return JSON.parse(insuredTierOptions);
        },

        setDefaultInsuredTier: function (options) {
            defaultInsuredTier = options;
        },

        getDefaultInsuredTier: function () {
            return defaultInsuredTier;
        },

        setLabelStartPositionOptions: function (options) {
            labelStartPositionOptions = options;
        },

        getLabelStartPositionOptions: function () {
            if (!labelStartPositionOptions) {
                return null;
            }

            return JSON.parse(labelStartPositionOptions);
        },

        setShowToolbar: function (showToolbar) {
            showGridToolbar = showToolbar;
        },

        getShowToolbar: function () {
            return showGridToolbar;
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
        },

        /**
         * @param value
         */
        setGuaranteedIsActive : function (value) {
            isGuaranteedActive = value;
        },

        /**
         * Returns true or false.
         *
         * @returns {number}
         */
        getGuaranteedIsActive : function () {
            return isGuaranteedActive;
        },

        inCargoProducts: function (code) {
            var array = ['3606','3607','3608','3609','3610','3630','3657'];
            return $.inArray(code, array) >= 0;
        },

        inPackagesProducts: function (code) {
            var array = ['3083','3084','3085','3087','3089','3090','3094','3096','3189','3385','3389','3390'];
            return $.inArray(code, array) >= 0;
        },

        inExtraCover: function (code) {
            var array = ['3087', '3094', '3443', '3446', '3544', '3534', '3581', '3584', '4878', '4914', '4965'];
            return $.inArray(code, array) >= 0;
        }

    };
});
