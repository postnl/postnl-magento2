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
                {text: $.mage.__('Change parcel count'), value: 'change_parcel'}
            ]),
            defaultOption : ko.observable(DataProvider.getDefaultOption()),
            optionList : ko.observableArray(
                DataProvider.getProductOptions()
            )
        },

        /**
         * Init.
         *
         * @returns {exports}
         */
        initObservable : function () {
            this._super().observe([
                'currentSelected'
            ]);

            this.currentSelected.subscribe(function (value) {
                // Selection is changed.
            });

            return this;
        },

        /**
         * The PostNL toolbar should only be visable on the order and shipment grid.
         *
         * @returns {boolean}
         */
        showPostNLToolbarActions : function () {
            return this.ns === 'sales_order_grid' || this.ns === 'sales_order_shipment_grid';
        },

        /**
         * Submit selected items and postnl form data to controllers
         * - MassChangeMulticolli
         * - MassChangeProduct
         */
        submit : function () {
            var data = this.getSelectedItems();
            var value = $('#'+this.currentSelected())[0].value;
            if (isNaN(parseInt(value))) {
                alert(DataProvider.getInputWarningMessage(this.currentSelected()));
                return;
            }

            data[this.currentSelected()] = value;

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
