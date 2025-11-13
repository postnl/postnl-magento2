define(['jquery'], function($) {
    'use strict';

    return function() {
        if ($('body').hasClass('checkout-cart-index')) {
            const $countrySelect = $('select[name="country-id"]');
            const $buttonContainer = $('.button--postnl-container');

            function toggleCartButton() {
                const selectedCountry = $countrySelect.val();
                if (selectedCountry === 'NL') {
                    $buttonContainer.show();
                } else {
                    $buttonContainer.hide();
                }
            }

            toggleCartButton();
            $countrySelect.on('change', function () {
                setTimeout(toggleCartButton, 300);
            });
        }
    };
});
