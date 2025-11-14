<?php
declare(strict_types=1);

namespace TIG\PostNL\Block\Frontend\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;
use TIG\PostNL\Model\Config;
use function __;

class LayoutProcessor implements LayoutProcessorInterface
{
    private Config $config;

    private Session $checkoutSession;

    private UrlInterface $urlBuilder;

    public function __construct(
        Config $config,
        Session $checkoutSession,
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
    }

    public function process($jsLayout): array
    {
        if ($this->config->isEnabled() && $this->config->isEnabledInCheckout()) {
            $quote = $this->checkoutSession->getQuote();
            if ($this->checkoutSession->getData('fillin_data')) {
                $address = $quote->getShippingAddress();
                $js = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children'];
                $js['firstname']['value'] = $address->getFirstname();
                $js['lastname']['value'] = $address->getLastname();
                $js['email']['value'] = $address->getEmail();
                $js['city']['value'] = $address->getCity();
                $js['street']['children'][0]['value'] = $address->getStreetLine(0);
                $js['street']['children'][1]['value'] = $address->getStreetLine(1);
                $js['street']['children'][2]['value'] = $address->getStreetLine(2);
                $js['postcode']['value'] = $address->getPostcode();

                $js['postcode-field-group']['children']['field-group']['children']
                ['housenumber']['value'] = $this->checkoutSession->getData('postnl_housenumber');
                $js['postcode-field-group']['children']['field-group']['children']
                ['housenumber_addition']['value'] = $this->checkoutSession->getData('postnl_housenumberaddition');
            } elseif ($quote->getCustomerIsGuest()) {
                $js = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children'];
                $new = [
                    'fillin' => [
                        'component' => 'TIG_PostNL/js/view/fillin-button',
                        'displayArea' => 'fillin-button',
                        'config' => [
                            'template' => 'TIG_PostNL/fillin-button',
                            'buttonText' => __('Fill in with PostNL.'),
                            'redirectUrl' => $this->urlBuilder->getUrl('postnl/fillin/index'),
                            'buttonClass' => 'action primary'
                        ]
                    ]
                ];
                if ($this->config->getCheckoutDisplayPosition() === Config\Source\Checkout::CHECKOUT_BEFORE_EMAIL) {
                    $js['customer-email']['children']['before-login-form']['children'] += $new;
                } elseif (
                    $this->config->getCheckoutDisplayPosition() === Config\Source\Checkout::CHECKOUT_BEFORE_DETAILS
                ) {
                    $js['before-form']['children'] += $new;
                }
            }
        }

        return $jsLayout;
    }
}
