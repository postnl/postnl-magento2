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
// @codingStandardsIgnoreFile
// Credits: https://github.com/tig-nl/postnl-magento2/pull/175
define(
    [
        'underscore',
        'ko',
        'jquery',
        'Magento_Ui/js/lib/view/utils/async',
        'TIG_PostNL/js/Helper/State',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/quote',
        'Amasty_CheckoutCore/js/view/utils',
        'Amasty_CheckoutCore/js/model/payment/payment-loading',
        'Amasty_CheckoutCore/js/action/get-totals',
        'Amasty_CheckoutCore/js/model/shipping-registry',
        'Amasty_CheckoutCore/js/model/address-form-state',
        'Amasty_CheckoutCore/js/model/events',
        'uiRegistry',
        'rjsResolver'
    ],
    function (
        _,
        ko,
        $,
        async,
        State,
        storage,
        checkoutData,
        shippingService,
        selectShippingMethod,
        setShippingInformationAction,
        quote,
        viewUtils,
        paymentLoader,
        totalsProcessor,
        shippingRegistry,
        addressFormState,
        events,
        registry,
        onLoad
    ) {
        'use strict';

        var instance = null;

        function removeAmazonPayButton() {
            var amazonPaymentButton = $('#PayWithAmazon_amazon-pay-button img');

            if (amazonPaymentButton.length > 1) {
                amazonPaymentButton.not(':first').remove();
            }
        }

        return function (Shipping) {
            return Shipping.extend({
                allowedDynamicalSave: false,
                allowedDynamicalValidation: true,
                isUpdateCancelledByBilling: false,
                isInitialDataSaved: false,
                previousShippingMethodData: {},

                initialize: function () {
                    this._super();

                    instance = this;
                    onLoad(shippingRegistry.initObservers.bind(shippingRegistry, this.elems));
                    onLoad(this.registerObserversAfterLoad.bind(this));

                    if (!this.isFormInline) {
                        shippingRegistry.excludedCollectionNames.push(
                            'shipping-address-fieldset',
                            'additional-fieldsets'
                        );
                    }

                    // eslint-disable-next-line max-len
                    registry.get('checkout.steps.shipping-step.shippingAddress.before-form.amazon-widget-address.before-widget-address.amazon-checkout-revert',
                        function (component) {
                            component.isAmazonAccountLoggedIn.subscribe(this.amazonLoginStatusObserver);
                        }.bind(this));

                    registry.get('checkout.steps.billing-step.payment.payments-list.amazon_payment',
                        function (component) {
                            if (component.isAmazonAccountLoggedIn()) {
                                $('button.action-show-popup').hide();
                            }
                        });

                    // eslint-disable-next-line max-len
                    registry.get('checkout.steps.shipping-step.shippingAddress.customer-email.amazon-button-region.amazon-button',
                        function (component) {
                            async.async({
                                selector: '#PayWithAmazon_amazon-pay-button img'
                            }, function () {
                                removeAmazonPayButton();
                            });

                            component.isAmazonAccountLoggedIn.subscribe(function (loggedIn) {
                                if (!loggedIn) {
                                    removeAmazonPayButton();
                                }
                            });
                        });

                    quote.billingAddress.subscribe(function () {
                        if (this.isUpdateCancelledByBilling) {
                            this.validateAndSaveIfChanged();
                        }
                    }, this);
                },

                initObservable: function () {
                    this._super();

                    quote.shippingMethod.subscribe(this.shippingMethodObserver.bind(this));

                    if (quote.shippingMethod.getVersion() > 1) {
                        this.shippingMethodObserver(quote.shippingMethod());
                    }

                    this.allowedDynamicalSave = true;

                    events.onBeforeShippingSave(shippingRegistry.register.bind(shippingRegistry));
                    events.onBeforeShippingSave(paymentLoader.bind(null, true));
                    events.onAfterShippingSave(paymentLoader.bind(null, false));

                    return this;
                },

                /**
                 * Register subscribers who save shipping.
                 * Register after full load.
                 */
                registerObserversAfterLoad: function () {
                    this.isInitialDataSaved = true;

                    shippingRegistry.isAddressChanged.subscribe(this.additionalFieldsObserver.bind(this));
                    shippingService.isLoading.subscribe(function (isLoading) {
                        if (!isLoading) {
                            this.validateAndSaveIfChanged();
                        }
                    }, this);
                },

                /**
                 * If checkout already have all shipping information
                 * then execute validate and save process because we dont have any triggers
                 * and save should be executed on storefront for 3rd party extensions compatibility
                 * @returns {void}
                 */
                saveInitialData: function () {
                    if (!this.isInitialDataSaved) {
                        onLoad(function () {
                            if (this.silentValidation()) {
                                this.validateAndSaveIfChanged();
                            }
                        }.bind(this));

                        this.isInitialDataSaved = true;
                    }
                },

                /**
                 * Validate shipping without showing any errors
                 * @return {Boolean} result Validation result
                 */
                silentValidation: function () {
                    var invalidElement,
                        result = !_.isEmpty(quote.shippingMethod()) && !_.isEmpty(quote.shippingAddress());

                    if (result && this.isFormInline) {
                        invalidElement = _.find(shippingRegistry.addressComponents, function (module) {
                            return ko.isObservable(module.required) &&
                                ko.isObservable(module.value) &&
                                ko.isObservable(module.visible) &&
                                ko.isObservable(module.disabled) &&
                                module.required.peek() &&
                                module.visible.peek() &&
                                !module.disabled.peek() &&
                                _.isEmpty(module.value.peek());
                        });

                        result = _.isUndefined(invalidElement);
                    }

                    return result;
                },

                setShippingInformation: function () {
                    var result;

                    this.allowedDynamicalSave = false;
                    result = this._super();
                    this.allowedDynamicalSave = true;

                    return result;
                },

                selectShippingMethod: function (method) {
                    window.loaderIsNotAllowed = true;
                    this._super(method);
                    instance.validateAndSaveIfChanged();
                    delete window.loaderIsNotAllowed;

                    return true;
                },

                validateShippingInformation: function () {
                    var result;

                    this.allowedDynamicalValidation = false;
                    result = this._super();
                    this.allowedDynamicalValidation = true;

                    return result;
                },

                /**
                 * Calculate Totals for changed shipping method.
                 * Necessary only if dynamical shipping save is not working (i.e. shipping is not valid)
                 *
                 * @param {Object} method Shipping method
                 * @returns {void}
                 */
                shippingMethodObserver: function (method) {
                    this.saveInitialData();

                    if (method &&
                        shippingRegistry.isEstimationHaveError() ||
                        this.source.get('params.invalid') ||
                        shippingRegistry.isEstimationHaveError.getVersion() === 1 &&
                        shippingRegistry.isHaveUnsavedShipping()
                    ) {
                        totalsProcessor();
                    }
                },

                /**
                 * Reselect shipping method when customer logs out of Amazon
                 * @param {Boolean} loggedIn Amazon account login status
                 * @returns {void}
                 */
                amazonLoginStatusObserver: function (loggedIn) {
                    if (!loggedIn) {
                        registry.get('checkout.steps.shipping-step.shippingAddress', function (component) {
                            if (component.isSelected()) {
                                component.selectShippingMethod(quote.shippingMethod());
                            }
                        });
                    }
                },

                /**
                 * Trigger shipping address validation and save on additional address fields change
                 * @param {Boolean} isChanged Change status
                 * @returns {void}
                 */
                additionalFieldsObserver: function (isChanged) {
                    var versionBeforeChange;

                    if (!isChanged || shippingRegistry.isEstimationHaveError()) {
                        paymentLoader(false);

                        return;
                    }

                    if (this.isFormInline) {
                        versionBeforeChange = shippingService.isLoading.getVersion();

                        if ((this.validateShippingInformation() || !this.source.get('params.invalid')) &&
                            shippingService.isLoading.hasChanged(versionBeforeChange)
                        ) {
                            return;
                        }
                    }

                    this.validateAndSaveIfChanged();
                },

                validateAndSaveIfChanged: function () {
                    var isShippingValid;

                    if (!this.allowedDynamicalSave ||
                        this.isBillingAddressFormVisible() ||
                        !shippingRegistry.isHaveUnsavedShipping()
                    ) {
                        paymentLoader(false);

                        return;
                    }

                    isShippingValid = !this.allowedDynamicalValidation;

                    // allowedDynamicalValidation - for avoid circular dependency
                    if (this.allowedDynamicalValidation) {
                        this.allowedDynamicalSave = false;
                        isShippingValid = this.validateShippingInformation();
                        this.allowedDynamicalSave = true;
                    }

                    /*
                     if isFormInline = true, method validateShippingInformation
                     will set shipping address and this observer will be executed.
                     validateShippingInformation - also validate email, which is not part of Shipping information.
                    */
                    if (isShippingValid || (this.isFormInline && !this.source.get('params.invalid'))) {
                        window.loaderIsNotAllowed = true;
                        setShippingInformationAction();
                        delete window.loaderIsNotAllowed;
                    } else {
                        paymentLoader(false);
                    }
                },

                getNameShippingAddress: function () {
                    return viewUtils.getBlockTitle('shipping_address');
                },

                getNameShippingMethod: function () {
                    return viewUtils.getBlockTitle('shipping_method');
                },

                isPostNlEnable: function () {
                    return window.checkoutConfig.quoteData.posnt_nl_enable;
                },

                canUseDeliveryOption: function () {
                    var deliveryOptionsActive = window.checkoutConfig.shipping.postnl.shippingoptions_active == 1;
                    var deliveryDaysActive = window.checkoutConfig.shipping.postnl.is_deliverydays_active;
                    var pakjegemakActive = window.checkoutConfig.shipping.postnl.pakjegemak_active == '1';

                    return deliveryOptionsActive && (deliveryDaysActive || pakjegemakActive);
                },

                isPostNLDeliveryMethod: function (method) {
                    return method.carrier_code == 'tig_postnl';
                },

                canUsePostnlDeliveryOptions: function (method) {
                    if (!this.canUseDeliveryOption()) {
                        return false;
                    }

                    var result = this.isPostNLDeliveryMethod(method);

                    if (result) {
                        State.method(method);
                    }

                    return result;
                },

                /**
                 * Trigger Shipping data Validate Event.
                 * @returns {void}
                 */
                triggerShippingDataValidateEvent: function () {
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }
                },

                validatePlaceOrder: function () {
                    var loginFormSelector = 'form[data-role=email-with-possible-login]',
                        emailValidationResult = this.isCustomerLoggedIn();

                    if (!emailValidationResult) {
                        $(loginFormSelector).validation();
                        emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                    }

                    if (!emailValidationResult) {
                        $(loginFormSelector + ' input[name=username]').focus();

                        return false;
                    }

                    if (this.isFormInline) {
                        this.source.set('params.invalid', false);
                        this.triggerShippingDataValidateEvent();

                        if (
                            this.source.get('params.invalid')
                        ) {
                            return false;
                        }
                    }

                    return true;
                },

                isModernDesign: function () {
                    return window.checkoutDesign === 'modern';
                },

                /**
                 * @param {Object} method - shipping method
                 * @return {String} comment Tooltip text
                 */
                getMethodTooltipText: function (method) {
                    var comment = '';

                    if (this.isModernDesign() && method.error_message) {
                        comment = method.error_message;
                    }

                    if (!comment) {
                        comment = this.getCommentShippingMethod(method);
                    }

                    return comment;
                },

                /**
                 * Compatibility with Amasty_ShippingTableRates and Amasty_StorePickup
                 * @param {Object} method Shipping method
                 * @returns {String} Shipping method comment
                 */
                getCommentShippingMethod: function (method) {
                    if (!method) {
                        return '';
                    }

                    if (method.comment && typeof method.comment === 'string') {
                        return method.comment;
                    }

                    if (method.extension_attributes) {
                        if (method.extension_attributes.amstorepick_comment) {
                            return method.extension_attributes.amstorepick_comment;
                        }

                        if (method.extension_attributes.amstartes_comment) {
                            return method.extension_attributes.amstartes_comment;
                        }
                    }

                    return '';
                },

                getAdditionalClassForIcons: function (method) {
                    if (this.isModernDesign() &&
                        Object.prototype.hasOwnProperty.call(method, 'error_message') &&
                        method.error_message
                    ) {
                        return '-error';
                    }

                    return '';
                },

                isShippingMethodTooltip: function (method) {
                    return this.isModernDesign() && this.getMethodTooltipText(method);
                },

                getColspanCarrier: function (method) {
                    if (this.isShippingMethodTooltip(method)) {
                        return 1;
                    }

                    return 2;
                },

                /**
                 * Disable focus on "silent" validation
                 * "silent" validation can be triggered by payment additional validator
                 * @returns {Function} focusInvalid parent function
                 */
                focusInvalid: function () {
                    if (!window.silentShippingValidation) {
                        this._super();
                    }

                    return this;
                },

                /**
                 * Check on visible billing address form
                 * @returns {Boolean} Billing address form visibility state
                 */
                isBillingAddressFormVisible: function () {
                    this.isUpdateCancelledByBilling = !addressFormState.isBillingSameAsShipping() &&
                        addressFormState.isBillingFormVisible();

                    return this.isUpdateCancelledByBilling;
                },

                /**
                 * Can be used as override for getPopUp function
                 * to suppress new shipping address popup.
                 * Used in 'list-mixin.js'
                 *
                 * @return {{openModal: openModal, closeModal: closeModal}}
                 */
                getPopUpOverride: function () {
                    var self = this;

                    return {
                        openModal: function () {
                            self.isFormPopUpVisible(true);
                        },
                        closeModal: function () {
                            self.isFormPopUpVisible(false);
                        }
                    };
                }
            });
        };
    }
);
