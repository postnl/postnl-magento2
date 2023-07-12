define([], function () {
    return {
        log: function () {
            console.log.apply(console, arguments);
        },

        info: function () {
            console.info.apply(console, arguments);
        },

        error: function () {
            console.error.apply(console, arguments);
        }
    };
});
