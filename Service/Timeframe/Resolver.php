<?php
namespace TIG\PostNL\Service\Timeframe;

use Magento\Checkout\Model\Session;
use TIG\PostNL\Config\CheckoutConfiguration\IsDeliverDaysActive;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipping\BoxablePackets;
use TIG\PostNL\Service\Shipping\DeliveryDate;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Service\Validation\CountryShipping;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;

class Resolver
{
    /**
     * @var array
     */
    protected array $returnErrors = [
        0 => 'Could not load from soap data',
        1 => 'No Address data found.',
        2 => 'Deliverydays options are disabled.',
        3 => 'Invalid timeframes response, more information can be found in the PostNL log files.',
        4 => 'Invalid locations response, more information can be found in the PostNL log files.',
    ];

    private Session $checkoutSession;
    private LetterboxPackage $letterboxPackage;
    private BoxablePackets $boxablePackets;
    private IsDeliverDaysActive $isDeliveryDaysActive;
    private AddressEnhancer $addressEnhancer;
    private CountryShipping $countryShipping;
    private TimeFrame $timeFrameEndpoint;
    private ShippingOptions $shippingOptions;
    private DeliveryDate $deliveryDate;

    public function __construct(
        Session $checkoutSession,
        AddressEnhancer $addressEnhancer,
        TimeFrame $timeFrame,
        IsDeliverDaysActive $isDeliverDaysActive,
        ShippingOptions $shippingOptions,
        LetterboxPackage $letterboxPackage,
        BoxablePackets $boxablePackets,
        CountryShipping $countryShipping,
        DeliveryDate $deliveryDate
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->letterboxPackage = $letterboxPackage;
        $this->boxablePackets = $boxablePackets;
        $this->isDeliveryDaysActive = $isDeliverDaysActive;
        $this->addressEnhancer = $addressEnhancer;
        $this->countryShipping = $countryShipping;
        $this->timeFrameEndpoint = $timeFrame;
        $this->shippingOptions = $shippingOptions;
        $this->deliveryDate = $deliveryDate;
    }

    public function processTimeframes(array $address): array
    {
        if (!isset($address['country'])) {
            // Check country id?
            if (!isset($address['country_id'])) {
                return $this->getFallBackResponse();
            }
            $address['country'] = $address['country_id'];
        }

        $quote = $this->checkoutSession->getQuote();
        $cartItems = $quote->getAllItems();

        //Letterbox NL
        if ($address['country'] === 'NL' && $this->letterboxPackage->isLetterboxPackage($cartItems, false)) {
            return $this->getLetterboxPackageResponse();
        }

        //Boxable Packet = Letterbox Worldwide
        if ($address['country'] !== 'NL' && $this->boxablePackets->isBoxablePacket($cartItems, false)) {
            return $this->getBoxablePacketResponse();
        }

        if ($address['country'] === 'ES' && in_array($address['country'], EpsCountries::ALL, true) && $this->canaryConverter->isCanaryIsland($address)) {
            return $this->getGlobalPackResponse();
        }

        if ($this->isEpsCountry($address)) {
            return $this->getEpsCountryResponse();
        }

        if (!in_array($address['country'], EpsCountries::ALL, true) && !in_array($address['country'], ['BE', 'NL'], true)) {
            return $this->getGlobalPackResponse();
        }

        if (!$this->isDeliveryDaysActive->getValue()) {
            return $this->getFallBackResponse(2, );
        }

        $this->addressEnhancer->set($address);

        try {
            return $this->getValidResponseType();
        } catch (\Exception $exception) {
            return $this->getFallBackResponse(3, );
        }
    }

    private function isEpsCountry(array $address): bool
    {
        if (!in_array($address['country'], EpsCountries::ALL, true)) {
            return false;
        }

        // NL to BE/NL shipments are not EPS shipments
        if ($this->countryShipping->isShippingNLToEps($address['country'])) {
            return true;
        }

        //NL to BE shipments can be a PEPS shipment and should be considered as such when enabled
        if ($this->countryShipping->isShippingNLtoBE($address['country']) && $this->shippingOptions->canUsePriority()) {
            return true;
        }

        // BE to BE shipments is not EPS, but BE to NL is
        if ($this->countryShipping->isShippingBEToEps($address['country'])) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getPossibleDeliveryDays(array $address)
    {
        $startDate  = $this->deliveryDate->get($address);

        return $this->getTimeFrames($address, $startDate);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getValidResponseType(): array
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            return [
                'error'      => __('%1 : %2', $address['error']['code'], $address['error']['message']),
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }

        $timeframes = $this->getPossibleDeliveryDays($address);
        if (empty($timeframes)) {
            return [
                'error'      => __('No timeframes available.'),
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }

        return [
            'timeframes' => $timeframes
        ];
    }

    /**
     * CIF call to get the timeframes.
     *
     * @param array $address
     * @param $startDate
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getTimeFrames($address, $startDate)
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->timeFrameEndpoint->updateApiKey($storeId);
        $this->timeFrameEndpoint->fillParameters($address, $startDate);

        return $this->timeFrameEndpoint->call();
    }

    public function getFallBackResponse(int $error = 0): array
    {
        $errorMessage = $this->returnErrors[$error] ?? '';
        return [
            'error'      => __($errorMessage),
            'timeframes' => [[['fallback' => __('At the first opportunity')]]]
        ];
    }

    private function getLetterboxPackageResponse(): array
    {
        return [
            'letterbox_package' => true,
            'timeframes'        => [[[
                'letterbox_package' => __('Your order is a letterbox package and will be delivered from Tuesday to Saturday.')
            ]]]
        ];
    }

    private function getBoxablePacketResponse(): array
    {
        return [
            'boxable_packets'   => true,
            'timeframes'        => [[['boxable_packets' => __('Your order is a boxable packet.')]]]
        ];
    }

    private function getEpsCountryResponse(): array
    {
        return [
            'timeframes' => [[['eps' => __('Ship internationally')]]]
        ];
    }

    private function getGlobalPackResponse(): array
    {
        return [
            'timeframes' => [[['gp' => __('Ship internationally')]]]
        ];
    }
}
