<?php

namespace TIG\PostNL\Service\Wrapper;

interface CheckoutSessionInterface
{
    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote();

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getValue($key);

    /**
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function setData($key, $value);
}
