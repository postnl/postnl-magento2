<?php
namespace TIG\PostNL\Service\Timeframe;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\CacheInterface;
use TIG\PostNL\Config\CheckoutConfiguration\IsDeliverDaysActive;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Converter\CanaryIslandToIC;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipping\BoxablePackets;
use TIG\PostNL\Service\Shipping\DeliveryDate;
use TIG\PostNL\Service\Shipping\InternationalPacket;
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
    private InternationalPacket $internationalPacket;
    private IsDeliverDaysActive $isDeliveryDaysActive;
    private AddressEnhancer $addressEnhancer;
    private CountryShipping $countryShipping;
    private TimeFrame $timeFrameEndpoint;
    private DeliveryDate $deliveryDate;
    private CanaryIslandToIC $canaryConverter;
    private CacheInterface $cache;
    private ?string $lastRequestDate = null;

    public function __construct(
        Session $checkoutSession,
        AddressEnhancer $addressEnhancer,
        TimeFrame $timeFrame,
        IsDeliverDaysActive $isDeliverDaysActive,
        LetterboxPackage $letterboxPackage,
        BoxablePackets $boxablePackets,
        InternationalPacket $internationalPacket,
        CountryShipping $countryShipping,
        DeliveryDate $deliveryDate,
        CanaryIslandToIC $canaryConverter,
        CacheInterface $cache
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->letterboxPackage = $letterboxPackage;
        $this->boxablePackets = $boxablePackets;
        $this->internationalPacket = $internationalPacket;
        $this->isDeliveryDaysActive = $isDeliverDaysActive;
        $this->addressEnhancer = $addressEnhancer;
        $this->countryShipping = $countryShipping;
        $this->timeFrameEndpoint = $timeFrame;
        $this->deliveryDate = $deliveryDate;
        $this->canaryConverter = $canaryConverter;
        $this->cache = $cache;
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
        if ($address['country'] !== 'NL' && $this->boxablePackets->canFixInTheBox($cartItems)) {
            return $this->getBoxablePacketResponse();
        }

        //International Packet
        if ($address['country'] !== 'NL' && $this->internationalPacket->canFixInTheBox($cartItems)) {
            return $this->getInternationalCountryResponse();
        }

        if ($address['country'] === 'ES' && in_array($address['country'], EpsCountries::ALL, true)
            && $this->canaryConverter->isCanaryIsland($address)
        ) {
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

        // BE to BE shipments is not EPS, but BE to EU is
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
        $this->lastRequestDate = $startDate;
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

        $cacheId = $this->getIdentifier($address);
        $content = $this->cache->load($cacheId);
        if ($content) {
            try {
                $data = \json_decode($content, true, 32, JSON_THROW_ON_ERROR);
                if (is_array($data) && isset($data['tf'])) {
                    $this->checkoutSession->setPostNLDeliveryDate($data['date']);
                    return ['timeframes' => $data['tf']];
                }
            } catch (\Exception $e) {
                // retrieve data from api
            }
        }
        $timeframes = $this->getPossibleDeliveryDays($address);
        if (empty($timeframes)) {
            return [
                'error'      => __('No timeframes available.'),
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }
        $cache = [
            'tf' => $timeframes,
            'date' => $this->lastRequestDate
        ];

        $this->cache->save(\json_encode($cache), $cacheId, ['POSTNL_REQUESTS'], 60 * 5);

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
            'timeframes'        => [[['boxable_packets' => __('Ship internationally')]]]
        ];
    }

    private function getInternationalCountryResponse(): array
    {
        return [
            'boxable_packets'   => true,
            'timeframes'        => [[['international_packet' => __('Ship internationally')]]]
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

    private function getIdentifier(array $address): string
    {
        return 'POSTNL_TIMFRAME_' . $address['country'] . '_' . $address['housenumber'] . $address['postcode'];
    }
}
