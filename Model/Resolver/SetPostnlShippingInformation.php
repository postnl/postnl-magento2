<?php

declare(strict_types=1);

namespace TIG\PostNL\Model\Resolver;

use Magento\Checkout\Model\Session;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\MaskedQuoteIdToQuoteId;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Action\OrderSave;
use TIG\PostNL\Service\Timeframe\Resolver;

class SetPostnlShippingInformation implements ResolverInterface
{
    protected array $cache = [];

    public function __construct(
        protected Session $checkoutSession,
        protected MaskedQuoteIdToQuoteId $maskedQuoteIdToQuoteId,
        protected QuoteRepository $quoteRepository,
        protected OrderSave $orderSave,
        protected Resolver $timeframeResolver,
        protected OrderRepositoryInterface $orderRepository
    ) {
    }
    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $cartId = $this->maskedQuoteIdToQuoteId->execute($args['input']['cart_id']);
        /** @var Quote $quote */
        $quote = $this->quoteRepository->get($cartId);
        $this->checkoutSession->setQuote($quote);
        $this->checkoutSession->replaceQuote($quote);

        $shippingAddress = $quote->getShippingAddress();

        $request = [
            'type' => match ($args['input']['type']) {
                'delivery' => 'delivery',
                'pickup' => 'pickup',
                default => throw new GraphQlInputException(__('Invalid delivery type provided.')),
            },
            'country' => $shippingAddress->getCountryId(),
            'quote_id' => $quote->getId(),
            'address' => [
                'country' => $shippingAddress->getCountryId(),
                'street' => $shippingAddress->getStreet(),
                'postcode' => $shippingAddress->getPostcode(),
                'housenumber' => $shippingAddress->getStreetLine(2),
            ],
            'customerData' => [
                'country' => $shippingAddress->getCountryId(),
                'street' => $shippingAddress->getStreet(),
                'postcode' => $shippingAddress->getPostcode(),
                'housenumber' => $shippingAddress->getStreetLine(2),
                'firstname' => $shippingAddress->getFirstname(),
                'lastname' => $shippingAddress->getLastname(),
                'telephone' => $shippingAddress->getTelephone()
            ],
            'stated_address_only' => (bool)isset($args['input']['stated_address_only']) ? $args['input']['stated_address_only'] : false,
        ];

        if ($request['type'] === 'pickup') {
            $request = $this->processPickupRequest($request, $args['input']);
        }
        if ($request['type'] === 'delivery') {
            $request = $this->processDeliveryRequest($request, $args['input']);
        }

        $this->orderSave->saveDeliveryOption(
            $this->getPostnlOrder($quote),
            $request
        );

        return ['model' => $quote];
    }

    protected function getPostnlOrder(Quote $quote): OrderInterface
    {
        $quoteId = $quote->getId();

        if (!array_key_exists($quoteId, $this->cache)) {
            $this->cache[$quoteId] = $this->orderRepository->getByQuoteId($quoteId);
            if (!$this->cache[$quoteId]) {
                $this->cache[$quoteId] = $this->orderRepository->create();
            }
            // Re-check that this order wasn't created an saved, in this case we need a new one.
            if ($this->cache[$quoteId]->getOrderId()) {
                $this->cache[$quoteId] = $this->orderRepository->create();
            }
            // Be sure to set quote id in the new model
            $this->cache[$quoteId]->setQuoteId($quoteId);
        }

        return $this->cache[$quoteId];
    }

    public function processDeliveryRequest(array $request, array $input): array
    {
        $request['option'] = $input['option'] ?? null;
        $request['date'] = $input['date'] ?? null;
        $request['from'] = $input['from'] ?? null;
        $request['to'] = $input['to'] ?? null;

        return $request;
    }

    public function processPickupRequest(array $request, array $input): array
    {
        // TODO: Fully implement logic: https://github.com/postnl/postnl-magento2-hyva-checkout/blob/96dac6770ff055b24020db95c53fef2a788870d2/Magewire/SelectPickup.php#L194
        $pickupLocation = $this->getPickupLocation($input['pickup_location_id']);
        $request['option'] = $input['option'] ?? 'PG';
        $request['from'] = $input['from'] ?? '15:00:00';
        $request['date'] = $input['date'] ?? null;
        $request['to'] = $input['to'] ?? null;
        $request['LocationCode'] = $input['pickup_location_id'];
        $request['name'] = $pickupLocation?->getName();
        $request['RetailNetworkID'] = $pickupLocation?->getNetworkId();
        $request['address'] = $pickupLocation?->getAddressArray();

        return $request;
    }

    public function getPickupLocation(int $quoteId): ?\stdClass
    {
        // TODO: implement https://github.com/postnl/postnl-magento2-hyva-checkout/blob/96dac6770ff055b24020db95c53fef2a788870d2/Magewire/SelectPickup.php#L194
        return null;
    }
}

