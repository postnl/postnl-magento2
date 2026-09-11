<?php

namespace TIG\PostNL\Model\Notification;

use Magento\Framework\Notification\MessageInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;

class NewApiKeyNotification implements MessageInterface
{
    private const MESSAGE_IDENTITY = 'postnl_new_api_key_notification';

    public function __construct(
        private AccountConfiguration $accountConfiguration
    ) {
    }

    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    public function isDisplayed()
    {
        return $this->accountConfiguration->isModusLive() && !$this->accountConfiguration->isNewApiKeyUsed();
    }

    public function getText()
    {
        return __(
            'Your PostNL account is still using the old API key. Request a new, future-proof API key via '
            . '<a href="#">developer.postnl.nl</a> to keep using PostNL shipping without interruption.'
        );
    }

    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
