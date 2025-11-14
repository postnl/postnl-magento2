define([
    'uiComponent',
    'ko',
    'jquery',
    'mage/url',
    'mage/translate',
    'Magento_Checkout/js/model/quote'
], function (Component, ko, $, urlBuilder, $t, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'TIG_PostNL/fillin-button',
            postnlLogoUrl: require.toUrl('TIG_PostNL/images/postnl-logo.png'),
            tooltipUrl: require.toUrl('TIG_PostNL/images/tooltip.png'),
            buttonText: $t('Fill in with PostNL.'),
            headingText: $t('Fill in the details with PostNL'),
            tooltipText: $t('We\'ll copy your name and address from your PostNL account. So you don\'t have to enter them again!'),
            buttonClass: 'action primary',
            redirectUrl: '',
            isLoading: false,
            isButtonVisible: ko.observable(false)
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super();
            this.isLoading = ko.observable(false);
            let firstLoad = true;
            const self = this;

            function getSelectedCountry() {
                return $('select[name="country_id"]').val();
            }

            this.isCountryNL = ko.pureComputed(function () {
                let countryId = quote.shippingAddress() && quote.shippingAddress().countryId;
                let selectedCountryValue = getSelectedCountry();
                let buttonContainer = $('.button--postnl-container');

                if (firstLoad) {
                    firstLoad = false;

                    if (selectedCountryValue === 'NL') {
                        self.isButtonVisible(true);
                        buttonContainer.show();
                        return true;
                    }

                    if (!selectedCountryValue) {
                        setTimeout(function () {
                            selectedCountryValue = getSelectedCountry();
                            if (selectedCountryValue === 'NL') {
                                self.isButtonVisible(true);
                                buttonContainer.show();
                            }
                        }, 200);
                        return false;
                    }
                }

                if (selectedCountryValue === 'NL') {
                    self.isButtonVisible(true);
                    buttonContainer.show();
                } else {
                    self.isButtonVisible(false);
                    buttonContainer.hide();
                }

                return selectedCountryValue === 'NL';
            });

            return this;
        },

        /**
         * Button click handler
         */
        onClick: function () {
            if (this.redirectUrl) {
                this.isLoading(true);

                // Make AJAX request to backend URL
                $.ajax({
                    url: this.redirectUrl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    success: function (response) {
                        this.handleSuccess(response);
                    }.bind(this),
                    error: function (xhr, status, error) {
                        this.handleError(xhr, status, error);
                    }.bind(this),
                    complete: function () {
                        this.isLoading(false);
                    }.bind(this)
                });
            }
        },

        /**
         * Handle successful response
         * @param {Object} response
         */
        handleSuccess: function (response) {
            if (response.redirect_url) {
                location.href = response.redirect_url;
            }
        },

        /**
         * Handle error response
         * @param {Object} xhr
         * @param {String} status
         * @param {String} error
         */
        handleError: function (xhr, status, error) {
            var errorMessage = $t('An error occurred. Please try again.');
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            console.error('Error:', errorMessage);
        },

        /**
         * Check if button should be disabled
         * @returns {Boolean}
         */
        isDisabled: function () {
            return this.isLoading() || !this.redirectUrl;
        },

        /**
         * Get button text with loading state
         * @returns {String}
         */
        getButtonText: function () {
            return this.isLoading() ? $t('Loading...') : this.buttonText;
        },

        /**
         * Get heading text with loading state
         * @returns {String}
         */
        getHeadingText: function () {
            return this.isLoading() ? $t('Loading...') : this.headingText;
        },

        /**
         * Get tooltip text with loading state
         * @returns {String}
         */
        getTooltipText: function () {
            return this.isLoading() ? $t('Loading...') : this.tooltipText;
        }
    });
});
