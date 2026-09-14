<?php
declare(strict_types=1);

namespace TIG\PostNL\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use function __;
use function hash;
use function in_array;

class NotificationAboutApiKey implements MessageInterface
{
    private const API_DOCUMENTATION_URL = 'https://developer.postnl.nl/integration-with-postnl/api-overview/future-proof-api-s/plug-ins/';

    public function __construct(
        private readonly AccountConfiguration $configuration
    ) {}

    public function getIdentity(): string
    {
        return hash('sha256', 'POSTNL_NOTIFICATION');
    }

    public function isDisplayed(): bool
    {
        return in_array($this->configuration->checkApiKeys(), ['Old', 'Empty']);
    }

    public function getText(): string
    {
        if ($this->configuration->checkApiKeys() === 'Old') {
            return
                __(
                    '<strong>Important:</strong> In the latest update of the extension, an additional API key field has been added to the account configuration of the PostNL extension.'
                )
                . '<br/>'
                . __(
                    'This field must be filled in with the new API key that can be obtained via the <a href="%url">Self Service module on the PostNL Business Portal</a>.',
                    ['url' => self::API_DOCUMENTATION_URL]
                )
                . '<br/>' . __(
                    'This API key is required to gain access to the new APIs that will be rolled out in a future update of the extension.'
                )
                . '<br/>' . __(
                    'It is very important that this key is entered before the relevant update is performed; otherwise, no connection can be made to the new PostNL APIs, and it will not be possible to create labels or use checkout features such as delivery days and pickup points.'
                );
        }

        if ($this->configuration->checkApiKeys() === 'Empty') {
            return
                __('<strong>Important:</strong> PostNL is migrating to the new Future-proof API v4.')
                . '<br/>' . __(
                    'Don\'t you have an API key yet, or do you have an API key issued before September 14, 2026?'
                )
                . '<br/>' . __(
                    'Request a new API key <a href="%url">here</a> to obtain your API key.',
                    ['url' => self::API_DOCUMENTATION_URL]
                )
                . '<br />' . __(
                    'Enter your PostNL API key in the account configuration of the PostNL extension. This key is required to connect to the PostNL APIs.'
                )
                . '<br />' . __(
                    'Without it you cannot create labels or use checkout features such as delivery days and pickup points.'
                );
        }

        return '';
    }

    public function getSeverity(): int
    {
        return self::SEVERITY_MAJOR;
    }
}
