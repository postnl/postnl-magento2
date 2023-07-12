define([
    'mageUtils'
], function (utils) {
    return function (downloadUrl, data) {
        utils.submit(
            { data: data },
            {
                target: '_blank',
                action: downloadUrl
            }
        );

        var monitorInterval = window.setInterval(function () {
            if (document.hasFocus()) {
                window.location.reload()
                window.clearInterval(monitorInterval)
            }
        }, 500)
    };
});
