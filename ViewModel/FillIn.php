<?php
declare(strict_types=1);

namespace TIG\PostNL\ViewModel;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Model\Config;

class FillIn implements ArgumentInterface
{
    private UrlInterface $urlInterface;

    private Config $config;

    private ScopeConfigInterface $scopeConfig;

    private CheckoutSession $checkoutSession;

    private CustomerSession $customerSession;

    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        UrlInterface $urlInterface
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Get the default country configured in the Magento backend.
     */
    public function getDefaultCountry(): ?string
    {
        return $this->scopeConfig->getValue(
            'general/country/default',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Can display for cart
     */
    public function canDisplayInCart(string $layoutName): bool
    {
        if (!$this->config->isEnabledInCart()) {
            return false;
        }

        $configPosition = $this->getCartPosition();

        return $layoutName === $configPosition;
    }

    /**
     * Can display for minicart
     */
    public function canDisplayInMinicart(string $layoutName): bool
    {
        if (!$this->config->isEnabledInMiniCart()) {
            return false;
        }

        $configPosition = $this->getMinicartPosition();

        return $layoutName === $configPosition;
    }

    /**
     * Get URL for the FillIn button
     */
    public function getFillInUrl(): string
    {
        return $this->urlInterface->getUrl('postnl/fillin/index');
    }

    /**
     * Get the minicart position
     */
    public function getMinicartPosition(): string
    {
        return $this->config->getMiniCartDisplayPosition();
    }

    /**
     * Get the cart position
     */
    public function getCartPosition(): string
    {
        return $this->config->getCartDisplayPosition();
    }

    /**
     * Get the checkout position
     */
    public function getCheckoutPosition(): string
    {
        return $this->config->getCheckoutDisplayPosition();
    }

    /**
     * Check if a customer is logged in
     */
    public function isLoggedIn(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get the shipping country from the checkout session
     */
    public function getShippingCountry(): ?string
    {
        $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();
        if ($shippingAddress) {
            return $shippingAddress->getCountryId();
        }

        return null;
    }
}
