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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Plugin\Postcodecheck\Management;

use Magento\Customer\Model\Metadata\Form;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\Webshop;

class CustomerForm
{
    const TIG_ENABLE_POSTCODE_CHECK = 'tig_postnl/addresscheck/enable_postcodecheck';

    /** @var Webshop */
    private $webshopConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Webshop              $webshopConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Webshop $webshopConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->webshopConfig = $webshopConfig;
        $this->scopeConfig   = $scopeConfig;
    }

    /**
     * @param Form             $subject
     * @param                  $result
     * @param RequestInterface $request
     *
     * @return mixed
     * @see    \Magento\Customer\Model\Metadata\Form::extractData
     * @plugin after
     *
     */
    public function afterExtractData(Form $subject, $result, RequestInterface $request)
    {
        $isEnabled = $this->scopeConfig->getValue(
            self::TIG_ENABLE_POSTCODE_CHECK,
            ScopeInterface::SCOPE_STORE
        );

        if (!$isEnabled || !array_key_exists('street', $result) || $request->getPostValue('country_id') != 'NL') {
            return $result;
        }

        $housenumber     = $request->getPostValue('tig-housenumber');
        $housenrAddition = $request->getPostValue('tig-housenumber-addition');

        $result['street'] = [
            $result['street'][0],
            $housenumber,
            $housenrAddition
        ];

        return $result;
    }
}
