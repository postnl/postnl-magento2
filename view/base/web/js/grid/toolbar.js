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
define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/grid/toolbar',
    'mageUtils',
    'TIG_PostNL/js/grid/dataprovider'
], function (
    $,
    ko,
    _,
    Toolbar,
    utils,
    DataProvider
) {
    'use strict';
    return Toolbar.extend({
        defaults : {
            currentSelected : ko.observable('change_product'),
            selectProvider: 'ns = ${ $.ns }, index = ids',
            modules: {
                selections: '${ $.selectProvider }'
            },
            actionList : ko.observableArray([
                {text: $.mage.__('Change productcode'), value: 'change_product'},
                {text: $.mage.__('Change parcel count'), value: 'change_parcel'},
                {text: $.mage.__('Choose printing start position'), value: 'choose_print_start_position'}
            ]),
            defaultOption : ko.observable(DataProvider.getDefaultOption()),
            optionList : ko.observableArray(
                DataProvider.getProductOptions()
            ),
            labelStartPositionOptions : ko.observable(DataProvider.getLabelStartPositionOptions()),
            startPositionSelected : ko.observable('0'),
            showToolbar : ko.observable(DataProvider.getShowToolbar()),
            jsLoaded : true,
            isGuaranteedActive : ko.observable(DataProvider.getGuaranteedIsActive()),
            showTimeOptions : ko.observable(false),
            timeOptions : ko.observable(DataProvider.getPackagesTimeOptions()),
            timeOptionSelected : ko.observable('1000'),
            showInsuredTiers: ko.observable(false),
            insuredTiers: ko.observable(DataProvider.getInsuredTierOptions()),
            defaultInsuredTier: ko.observable(DataProvider.getDefaultInsuredTier())
        },

        /**
         * Init.
         *
         * @returns {exports}
         */
        initObservable : function () {
            this._super().observe([
                'currentSelected',
                'showTimeOptions',
                'showInsuredTiers'
            ]);

            this.currentSelected.subscribe(function (value) {
                if (value !== 'change_product') {
                    self.showTimeOptions(false);
                    self.showInsuredTiers(false);
                    return;
                }

                self.toggleTimeOptions(self.defaultOption());
                self.toggleInsuredTiers(self.defaultOption());
            });

            this.toggleTimeOptions(DataProvider.getDefaultOption());
            this.toggleInsuredTiers(DataProvider.getDefaultOption());

            var self = this;
            this.defaultOption.subscribe(function (value) {
                self.toggleTimeOptions(value);
                self.toggleInsuredTiers(value);
            });

            return this;
        },

        toggleTimeOptions: function (value) {
            if (!this.isGuaranteedActive) {
                this.showTimeOptions(false);
                return;
            }

            if (DataProvider.inCargoProducts(value)) {
                this.showTimeOptions(true);
                this.timeOptions(DataProvider.getCargoTimeOptions());
                return;
            }

            if (DataProvider.inPackagesProducts(value)) {
                this.showTimeOptions(true);
                this.timeOptions(DataProvider.getPackagesTimeOptions());
                return;
            }

            return this.showTimeOptions(false);
        },

        toggleInsuredTiers: function (value) {
            if (DataProvider.inExtraCover(value)) {
                this.showInsuredTiers(true);
                return;
            }

            return this.showInsuredTiers(false);
        },

        /**
         * The PostNL toolbar should only be visable on the order and shipment grid.
         *
         * @returns {boolean}
         */
        showPostNLToolbarActions : function () {
            return this.showToolbar() == 1 && (this.ns === 'sales_order_grid' || this.ns === 'sales_order_shipment_grid');
        },

        /**
         * Submit selected items and postnl form data to controllers
         * - MassChangeMulticolli
         * - MassChangeProduct
         */
        submit : function (isSticky) {
            // Grab the input values of the regular toolbar or the sticky toolbar
            var selector = $('.' + this.currentSelected() + '_toolbar');
            if (isSticky) {
                selector = $('.' + this.currentSelected() + '_sticky');
            }

            var data = this.getSelectedItems();
            if (data.selected === false) {
                alert($.mage.__('Please select item(s)'));
                return;
            }

            var value = selector[0].value;
            if (isNaN(parseInt(value))) {
                alert(DataProvider.getInputWarningMessage(this.currentSelected()));
                return;
            }

            data[this.currentSelected()] = value;
            if (this.isGuaranteedActive() && this.showTimeOptions()) {
                if (isSticky) {
                    data.time = $('.time_option_sticky')[0].value;
                } else {
                    data.time = $('.time_option_toolbar')[0].value;
                }
            }

            if (this.showInsuredTiers()) {
                if (isSticky) {
                    data.insuredtier = $('.insured_tier_sticky')[0].value;
                } else {
                    data.insuredtier = $('.insured_tier_toolbar')[0].value;
                }
            }

            utils.submit({
                url: DataProvider.getSubmitUrl(this.currentSelected(), this.ns),
                data: data
            });
        },

        isNumeric : function (value) {
            return !isNaN(parseInt(value)) && isFinite(value);
        },

        /**
         * Obtain and return the selected items from the grid
         *
         * @returns {*}
         */
        getSelectedItems : function () {
            var provider = this.selections();
            var selections = provider && provider.getSelections();
            var itemsType = selections.excludeMode ? 'excluded' : 'selected';

            var selectedItems = {};
            selectedItems[itemsType] = selections[itemsType];

            if (!selectedItems[itemsType].length) {
                selectedItems[itemsType] = false;
            }

            // Params includes extra data like filters
            _.extend(selectedItems, selections.params || {});

            return selectedItems;
        }
    });
});
