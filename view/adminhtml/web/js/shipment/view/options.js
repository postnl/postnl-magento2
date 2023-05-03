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
