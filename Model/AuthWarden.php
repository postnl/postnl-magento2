<?php
declare(strict_types=1);

namespace TIG\PostNL\Model;

use Exception;
use GuzzleHttp\Client;
use Magento\Checkout\Model\Session;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use function __;
use function base64_encode;
use function bin2hex;
use function hash;
use function http_build_query;
use function json_decode;
use function method_exists;
use function print_r;
use function random_bytes;
use function rtrim;

class AuthWarden
{
    private const AUTH_WARDEN_URL = 'https://dil-login.postnl.nl';

    private Client $client;

    private Monolog $logger;

    private ManagerInterface $messageManager;

    private CartRepositoryInterface $cartRepository;

    private Session $checkoutSession;

    private UrlInterface $urlBuilder;

    private Config $config;

    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        ManagerInterface $messageManager,
        Monolog $logger,
        Client $client
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->client = $client;
    }

    public function generateRedirect(): string
    {
        $verifier = $this->generateRandomString();

        $state = bin2hex(random_bytes(16));

        $this->checkoutSession->setData(Config::POSTNL_VERIFIER_ATTRIBUTE, $verifier);
        $this->checkoutSession->setData(Config::POSTNL_STATE_ATTRIBUTE, $state);

        if ($this->config->debugOn()) {
            $this->logger->debug('AuthWarden: generateRedirect state ' . $state);
            $this->logger->debug('AuthWarden: generateRedirect verifier ' . $verifier);
        }

        $params = [
            'client_id' => $this->getClientId(),
            'redirect_uri' => $this->getRedirectUri(),
            'response_type' => 'code',
            'scope' => 'base',
            'code_challenge' => $this->challengeFromVerifier($verifier),
            'code_challenge_method' => 'S256',
            'state' => $state
        ];


        return self::AUTH_WARDEN_URL . '/oauth2/authorize' . '?' . http_build_query($params);
    }

    public function handleCallback(string $code, string $redirectState): bool
    {
        $verifier = $this->checkoutSession->getData(Config::POSTNL_VERIFIER_ATTRIBUTE, true);
        $state = $this->checkoutSession->getData(Config::POSTNL_STATE_ATTRIBUTE, true);

        if ($state === null) {
            $this->logger->error('AuthWarden: stored state is empty');

            return false;
        }

        if ($verifier === null) {
            $this->logger->error('AuthWarden: redirect state is empty');

            return false;
        }

        if ($redirectState !== $state) {
            $this->logger->error('AuthWarden: state mismatch');

            return false;
        }
        if ($this->config->debugOn()) {
            $this->logger->debug('AuthWarden: handleCallback state ' . $state);
            $this->logger->debug('AuthWarden: handleCallback redirectState ' . $redirectState);
            $this->logger->debug('AuthWarden: handleCallback verifier ' . $verifier);
        }

        $accessToken = $this->fetchAccessToken($code, $verifier);

        $customerData = [];
        if ($accessToken !== null) {
            $customerData = $this->fetchUserInfo($accessToken);
        }

        if (isset($customerData['person'], $customerData['primaryAddress'])) {
            $quote = $this->checkoutSession->getQuote();
            if ($quote->getCustomerIsGuest()) {
                $address = $quote->getShippingAddress();
                $quote->setCustomerEmail($customerData['person']['email'] ?? null);
                $address->setFirstname($customerData['person']['givenName'] ?? null);
                $address->setLastname($customerData['person']['familyName'] ?? null);
                $address->setEmail($customerData['person']['email'] ?? null);
                $address->setStreet($customerData['primaryAddress']['streetName'] ?? null);
                $address->setCountryId($customerData['primaryAddress']['countryIso2'] ?? null);
                $address->setCity($customerData['primaryAddress']['cityName'] ?? null);
                $address->setPostcode($customerData['primaryAddress']['postalCode'] ?? null);

                $this->checkoutSession->setData('postnl_housenumber', $customerData['primaryAddress']['houseNumber']);
                $this->checkoutSession->setData('postnl_housenumberaddition', $customerData['primaryAddress']['houseNumberAddition']);
                $extensionAttributes = $address->getExtensionAttributes();
                if ($extensionAttributes) {
                    if (method_exists($extensionAttributes, 'setTigHousenumber')) {
                        $extensionAttributes->setTigHousenumber($customerData['primaryAddress']['houseNumber'] ?? null);
                    }
                    if (method_exists($extensionAttributes, 'setTigHousenumberAddition')) {
                        $extensionAttributes->setTigHousenumberAddition(
                            $customerData['primaryAddress']['houseNumberAddition'] ?? null
                        );
                    }
                }

                $this->cartRepository->save($quote);

                $this->checkoutSession->setData('fillin_data', true);
            }

            return true;
        }

        $this->messageManager->addErrorMessage(__('Something went wrong, please try again later'));

        return false;
    }

    private function generateRandomString(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function challengeFromVerifier(string $verifier): string
    {
        $hashed = hash('sha256', $verifier, true);

        return $this->base64UrlEncode($hashed);
    }

    private function fetchAccessToken(string $code, string $verifier)
    {
        $payload = [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getRedirectUri(),
            'code_verifier' => $verifier,
            'scope' => 'base',
            'code' => $code,
            'client_id' => $this->getClientId(),
        ];

        try {
            $request = $this->client->postAsync(
                self::AUTH_WARDEN_URL . '/oauth2/token/',
                [
                    'form_params' => $payload,
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Accept' => 'application/json'
                    ],
                    'verify' => true,
                    'connect_timeout' => 5,
                    'timeout' => 30
                ]
            )->wait();
            $response = $request->getBody()->getContents();

            if ($this->config->debugOn()) {
                $this->logger->debug('AuthWarden: token payload ' . print_r($payload, true));
                $this->logger->debug('AuthWarden: token code ' . $request->getStatusCode());
                $this->logger->debug('AuthWarden: token response ' . print_r($response, true));
            }
            $data = json_decode($response, true);

            return $data['access_token'];
        } catch (Exception $e) {
            $this->logger->error('AuthWarden: token ' . $e->getMessage());
        }

        return null;
    }

    private function fetchUserInfo(string $accessToken): array
    {
        try {
            $request = $this->client->getAsync(
                self::AUTH_WARDEN_URL . '/api/user_info/',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken
                    ],
                    'verify' => true,
                    'connect_timeout' => 5,
                    'timeout' => 30
                ]
            )->wait();
            $response = $request->getBody()->getContents();

            if ($this->config->debugOn()) {
                $this->logger->debug('AuthWarden: user_info accessToken ' . $accessToken);
                $this->logger->debug('AuthWarden: user_info code ' . $request->getStatusCode());
                $this->logger->debug('AuthWarden: user_info response ' . print_r($response, true));
            }

            return json_decode($response, true);
        } catch (Exception $e) {
            $this->logger->error('AuthWarden: user_info response ' . $e->getMessage());

            return [];
        }
    }

    private function getClientId(): string
    {
        return $this->config->getClientID();
    }

    private function getRedirectUri(): string
    {
        return $this->urlBuilder->getUrl('postnl/fillin/callback');
    }
}
