<?php

namespace TIG\PostNL\Service\Wrapper;

use Magento\Checkout\Model\Session as MagentoCheckoutSession;

class CheckoutSession implements CheckoutSessionInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @param MagentoCheckoutSession $session
     */
    public function __construct(
        MagentoCheckoutSession $session
    ) {
        $this->session = $session;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->session->getQuote();
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getValue($key)
    {
        $this->session->getData($key);
    }

    /**
     * @param string $key
     * @param        $value
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function setData($key, $value)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->session->setData($key, $value);
    }
}
