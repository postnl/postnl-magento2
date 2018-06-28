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
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/grid/toolbar',
    'mageUtils',
    'mage/url'
], function (
    $,
    ko,
    _,
    Toolbar,
    utils,
    url
) {
    return Toolbar.extend({
        defaults : {
            currentSelected : ko.observable('change_parcel'),
            selectProvider: 'ns = ${ $.ns }, index = ids',
            modules: {
                selections: '${ $.selectProvider }'
            }
        },

        initObservable : function () {
            this._super().observe([
                'currentSelected'
            ]);

            this.currentSelected.subscribe(function (value) {
                // Selection is changed.
            });

            return this;
        },

        showPostNLToolbarActions : function () {
            return this.ns === 'sales_order_grid' || this.ns === 'sales_order_shipment_grid';
        },

        getPostNLActionsList : function () {
            return [
                {name: $.mage.__('Change parcel'), value: 'change_parcel'},
                {name: $.mage.__('Change product'), value: 'change_product'}
            ];
        },

        submit : function () {
            /**
             * In the frontend, url.build() will take care of everything (base url, form key etc.)
             * and return the proper url. However, this doesn't seem to be the case in the backend,
             * the base url needs to be set manually. TODO: Find a correct way to set the base url
             */
            url.setBaseUrl('http://postnl223.local/testadmin/');
            var submitUrl = url.build('sales/order/massCancel/');


            /**
             * this will create a data json which will look similar to:
             * {
             *   change_parcel: "3", //data inserted in the new field
             *   selected: ["1", "2"], //Selected orders from the grid. "selected" key could be "excluded", depending on your filters
             *   filters: {....},
             *   namespace: "..."
             * }
             */
            var data = this.getSelectedItems();
            data[this.currentSelected()] = $('#'+this.currentSelected())[0].value;

            console.log(submitUrl);
            console.log(data);

            /**
             * utils.submit() will take care of everything; it will trigger a post request to the provided url,
             * handle the form key if necessary, and redirect you to the url. All that it needs is a full url
             * (so http://base.url/route/controller/action), and the post data.
             */
            utils.submit({
                url: submitUrl,
                data: data
            });
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

            console.log(selections);

            if (!selectedItems[itemsType].length) {
                selectedItems[itemsType] = false;
            }

            // Params includes extra data like filters
            _.extend(selectedItems, selections.params || {});

            return selectedItems;
        }
    });
});
