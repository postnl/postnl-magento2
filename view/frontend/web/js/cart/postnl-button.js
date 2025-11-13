define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/loader',
    'Magento_Customer/js/customer-data'
], function ($, Component, ko, loader, customerData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/delivery-options-button',
            fillInUrl: '',
            minicartPosition: '',
            defaultCountry: ''
        },

        initialize: function () {
            this._super();
            this.isPostNLVisible = ko.observable(false);
            this.fillInUrl = this.fillInUrl || '';
            this.isButtonDisabled = ko.observable(false);
            this.minicartPosition = this.minicartPosition || '';
            this.defaultCountry = this.defaultCountry || '';
            this.shippingCountry = this.shippingCountry || '';
            this.monitorCart();

            if (this.minicartPosition === 'before_buttons') {
                this.movePostnlButtonInMinicart();
            }

            if ($('body').hasClass('checkout-cart-index')) {
                this.initCountryObserver();
            }

            return this;
        },

        monitorCart: function () {
            const self = this;
            const cart = customerData.get('cart');

            // Subscribe to cart data changes
            cart.subscribe(function (cartData) {
                const itemCount = cartData.items ? cartData.items.length : 0;

                if (itemCount > 0) {
                    self.checkShippingCountry();
                } else {
                    self.isPostNLVisible(false);
                }
            });

            // Perform initial check on page load
            const initialCartData = cart();
            const initialItemCount = initialCartData.items ? initialCartData.items.length : 0;
            if (initialItemCount > 0) {
                self.checkShippingCountry();
            } else {
                self.isPostNLVisible(false);
            }
        },

        /**
         * Check the shipping country from the session (for non-cart pages)
         * If the country is NL, make the PostNL button visible
         */
        checkShippingCountry: function () {
            const self = this;

            if (self.shippingCountry && self.shippingCountry === 'NL') {
                self.isPostNLVisible(true);
            } else {
                if (self.defaultCountry === 'NL') {
                    self.isPostNLVisible(true);
                } else {
                    self.isPostNLVisible(false);
                }
            }
        },

        /**
         * Waits for the country select element to appear, then binds event.
         */
        initCountryObserver: function () {
            const self = this;

            const waitForCountrySelect = function (callback) {
                let attempts = 0;
                const maxAttempts = 20;
                const interval = setInterval(function () {
                    const $select = $('select[name="country_id"]');

                    if ($select.length && $select.is(':visible')) {
                        clearInterval(interval);
                        callback($select);
                    }

                    attempts++;
                    if (attempts >= maxAttempts) {
                        clearInterval(interval);
                    }
                }, 300);
            };

            waitForCountrySelect(function ($select) {
                function updatePostNLVisibility() {
                    const selectedCountry = $select.val();
                    const defaultCountry = self.defaultCountry;
                    const shouldShowButton = (selectedCountry === 'NL' || defaultCountry === 'NL');
                    self.isPostNLVisible(shouldShowButton);
                }

                // Initial check
                updatePostNLVisibility();

                // On change
                $select.on('change', function () {
                    const newSelectedCountry = $select.val();
                    const shouldShowButtonAgain = (newSelectedCountry === 'NL');
                    self.isPostNLVisible(shouldShowButtonAgain);
                });
            });
        },

        /**
         * Click handler for the PostNL button
         */
        onClickPostnlButton: function () {
            const self = this;
            this.isButtonDisabled(true);

            const button = $(event.target);
            const loaderElement = button.closest('.postnl-flex-wrapper');

            loaderElement.loader();
            loaderElement.loader('show');

            $.ajax({
                url: this.fillInUrl,
                type: 'POST',
                dataType: 'json',
                showLoader: true,
                success: function (response) {
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    }
                },
                complete: function () {
                    self.isButtonDisabled(false);
                }
            });
        },

        /**
         * Function to move the PostNL button in the minicart
         */
        movePostnlButtonInMinicart: function () {
            const postnlFlexWrapper = $('.block-minicart .postnl-flex-wrapper');
            const minicartContentWrapper = $('#minicart-content-wrapper');

            if (postnlFlexWrapper.length && minicartContentWrapper.length) {
                minicartContentWrapper.before(postnlFlexWrapper);
                postnlFlexWrapper.addClass('before-buttons');
                $(window).trigger('resize');
            }
        }
    });
});
