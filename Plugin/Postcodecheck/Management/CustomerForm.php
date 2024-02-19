<?php

namespace TIG\PostNL\Plugin\Postcodecheck\Management;

use Magento\Customer\Model\Metadata\Form;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
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
     * @var State
     */
    private $state;

    /**
     * @param Webshop              $webshopConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param State                $state
     */
    public function __construct(
        Webshop $webshopConfig,
        ScopeConfigInterface $scopeConfig,
        State $state
    ) {
        $this->webshopConfig = $webshopConfig;
        $this->scopeConfig   = $scopeConfig;
        $this->state = $state;
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
        if (!$this->shouldExtractTigData($result, $request)) {
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

    /**
     * Validate whether the TIG housenumber and addition should be extracted
     *
     * @param                  $result
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function shouldExtractTigData($result, RequestInterface $request)
    {
        $isEnabled = $this->scopeConfig->getValue(self::TIG_ENABLE_POSTCODE_CHECK, ScopeInterface::SCOPE_STORE);

        try {
            $areaCode = $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            return false;
        }

        if (
            $areaCode == Area::AREA_ADMINHTML ||
            !$isEnabled ||
            !array_key_exists('street', $result) ||
            $request->getPostValue('country_id') != 'NL'
        ) {
            return false;
        }

        return true;
    }
}
