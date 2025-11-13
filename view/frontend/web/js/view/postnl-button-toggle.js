define(['jquery'], function($) {
    'use strict';

    return function() {
        if ($('body').hasClass('checkout-cart-index')) {
            var $countrySelect = $('select[name="country-id"]');
            var $buttonContainer = $('.button--postnl-container');

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

