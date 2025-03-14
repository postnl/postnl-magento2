<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Shipping\BoxablePackets;
use TIG\PostNL\Service\Shipping\InternationalPacket;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Service\Shipping\PickupLocations;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;

class Locations extends AbstractDeliveryOptions
{
    /** @var AddressEnhancer */
    private $addressEnhancer;

    /** @var Calculator */
    private $priceCalculator;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;
    private PickupLocations $pickupLocations;
    private BoxablePackets $boxablePackets;
    private InternationalPacket $internationalPacket;

    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        Calculator $priceCalculator,
        ShippingDuration $shippingDuration,
        LetterboxPackage $letterboxPackage,
        PickupLocations $pickupLocations,
        BoxablePackets $boxablePackets,
        InternationalPacket $internationalPacket
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->priceCalculator   = $priceCalculator;
        $this->letterboxPackage = $letterboxPackage;

        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
        );
        $this->pickupLocations = $pickupLocations;
        $this->boxablePackets = $boxablePackets;
        $this->internationalPacket = $internationalPacket;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['address']['country']) || !is_array($params['address'])) {
            return $this->jsonResponse(__('No Address data found.'));
        }

        $products = $this->checkoutSession->getQuote()->getAllItems();
        $country = $params['address']['country'];
        if ($country === 'NL' && $this->letterboxPackage->isLetterboxPackage($products)) {
            return $this->jsonResponse([
                'error' => __('Pickup locations are disabled for Letterbox packages.')
            ]);
        }
        if ($country === 'BE' && (
                $this->boxablePackets->canFixInTheBox($products) ||
                $this->internationalPacket->canFixInTheBox($products)
            )
        ) {
            return $this->jsonResponse([
                'error' => __('Pickup locations are disabled for packets.')
            ]);
        }

        $this->addressEnhancer->set($params['address']);
        $price = $this->priceCalculator->getPriceWithTax($this->getRateRequest(), 'pakjegemak');

        try {
            return $this->jsonResponse([
                'price'       => $price['price'],
                'locations'   => $this->getValidResponeType(),
                'pickup_date' => $this->pickupLocations->getLastDeliveryDate()
            ]);
        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'error' => __('Invalid locations response, more information can be found in the PostNL log files.')
            ]);
        }
    }

    /**
     * @return array|\Magento\Framework\Phrase
     */
    private function getValidResponeType()
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            //@codingStandardsIgnoreLine
            return ['error' => __('%1 : %2', $address['error']['code'], $address['error']['message'])];
        }

        return $this->pickupLocations->get($address);
    }
}
