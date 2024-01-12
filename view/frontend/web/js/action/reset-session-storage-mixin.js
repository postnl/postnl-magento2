/* jshint ignore:start */
define([
    'mage/utils/wrapper'
], function (
    wrapper
) {
    'use strict';

    return function (setResetSessionAction) {
        return wrapper.wrap(setResetSessionAction, function (originalAction) {

            sessionStorage.removeItem("postnlPickupOption");
            sessionStorage.removeItem("postnlDeliveryOption");

            return originalAction();
        });
    };
});
/* jshint ignore:end */
