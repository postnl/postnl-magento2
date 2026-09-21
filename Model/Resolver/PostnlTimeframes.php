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
use TIG\PostNL\Service\Timeframe\Resolver;

class PostnlTimeframes implements ResolverInterface
{
    public function __construct(
        protected Session $checkoutSession,
        protected MaskedQuoteIdToQuoteId $maskedQuoteIdToQuoteId,
        protected Resolver $timeframeResolver
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
        /** @var \Magento\GraphQl\Model\Query\ContextInterface $context */
        if (!isset($value['model']) && empty($args['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        $cartId = $args['cart_id'] ? $this->maskedQuoteIdToQuoteId->execute($args['cart_id']) : $value['model']->getId();
        $this->checkoutSession->setQuoteId($cartId);
        /** @var Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        if (!isset($args['address'])) {
            $args['address'] = $quote->getShippingAddress();
        }

        $timeframes = $this->timeframeResolver->processTimeframes($args['address']);

        $timeframes['timeframes'] = array_merge(...$timeframes['timeframes']);

        return $timeframes;
    }
}

