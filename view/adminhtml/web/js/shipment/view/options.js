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
define(['jquery', 'uiComponent'], function ($, Component) {
    return function (config) {
        return Component.extend({
            defaults: {
                parcelCount: 1,
                editingParcelCount: false
            },
            initObservable: function () {
                this._super().observe([
                    'parcelCount',
                    'newParcelCount',
                    'editingParcelCount'
                ]);

                this.parcelCount(config.parcelCount);
                this.newParcelCount(config.parcelCount);

                return this;
            },
            setEditingParcelCount: function (value) {
                this.editingParcelCount(value);
            },
            saveParcelCount: function () {
                $.ajax({
                    context: this,
                    url: config.saveUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        shipmentId: config.shipmentId,
                        parcelCount: this.newParcelCount()
                    },
                    success: function (result) {
                        if (result.success) {
                            this.parcelCount(this.newParcelCount());
                        }
                    }
                }).always(function () {
                    this.editingParcelCount(false);
                });
            }
        });
    };
});
