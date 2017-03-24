<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Wrapper;

use Magento\Checkout\Model\Session\Proxy as MagentoCheckoutSession;

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
    public function setData($key, $value)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->session->setData($key, $value);
    }
}
