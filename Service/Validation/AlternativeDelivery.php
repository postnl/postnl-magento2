<?php
namespace TIG\PostNL\Service\Validation;

use TIG\PostNL\Config\Provider\ProductOptions;

class AlternativeDelivery
{
    private ProductOptions $productOptions;

    public const DELIVERY_MAP_SCALE = 'scale';
    public const DELIVERY_MAP_COUNTRY = 'country';
    public const DELIVERY_MAP_CODE = 'code';

    public const CONFIG_DELIVERY = 'delivery';
    public const CONFIG_BE = 'be';
    public const CONFIG_EPS = 'eps';
    public const CONFIG_GLOBALPACK = 'gp';
    public const CONFIG_PAKGEGEMAK = 'pagjegemak';
    public const CONFIG_PAKGEGEMAK_BE_DOMESTIC = 'be_domestic';

    public function __construct(
        ProductOptions $productOptions
    ) {
        $this->productOptions = $productOptions;
    }

    protected function retrieveAlternativeCode(string $configKey, float $quoteTotal): ?string
    {
        $deliveryMap = $this->getConfig($configKey);
        if (empty($deliveryMap)) {
            return null;
        }
        $usedScale = null;
        $usedCode = null;
        foreach ($deliveryMap as $mapRow) {
            $minAmount = $mapRow[self::DELIVERY_MAP_SCALE] ?? 0;
            if ($minAmount > 0 && $quoteTotal >= $minAmount && ($usedScale === null || $quoteTotal > $usedScale)) {
                $usedCode = $mapRow[self::DELIVERY_MAP_CODE];
                $usedScale = $minAmount;
            }
        }
        return $usedCode;
    }

    protected function retrieveAlternativeCodeForCountry(string $configKey, float $quoteTotal, string $countryId = null): ?string
    {
        $deliveryMap = $this->getConfig($configKey);
        if (empty($deliveryMap)) {
            return null;
        }
        $usedScale = null;
        $usedCode = null;
        foreach ($deliveryMap as $mapRow) {
            $minAmount = $mapRow[self::DELIVERY_MAP_SCALE] ?? 0;
            $country = $mapRow[self::DELIVERY_MAP_COUNTRY] ?? '*';
            if ($minAmount > 0 && $quoteTotal >= $minAmount && ($usedScale === null || $quoteTotal > $usedScale)) {
                if ($country === '*' || strpos($country, $countryId) !== false) {
                    $usedCode = $mapRow[self::DELIVERY_MAP_CODE];
                    $usedScale = $minAmount;
                }
            }
        }
        return $usedCode;
    }

    protected function getConfig(string $configKey): array
    {
        switch ($configKey) {
            case self::CONFIG_DELIVERY:
                return $this->productOptions->getAlternativeDeliveryMap();
            case self::CONFIG_BE:
                return $this->productOptions->getAlternativeBeMap();
            case self::CONFIG_EPS:
                return $this->productOptions->getAlternativeEpsMap();
            case self::CONFIG_GLOBALPACK:
                return $this->productOptions->getAlternativeGlobalpackMap();
            case self::CONFIG_PAKGEGEMAK:
                return $this->productOptions->getAlternativePakjegemakMap();
            case self::CONFIG_PAKGEGEMAK_BE_DOMESTIC:
                return $this->productOptions->getAlternativePakjeGemakBeDomesticMap();
            default:
                return [];
        }
    }

    public function getMappedCode(string $configKey, float $quoteTotal, string $country = null): ?string
    {
        switch ($configKey) {
            case self::CONFIG_DELIVERY:
            case self::CONFIG_BE:
            case self::CONFIG_PAKGEGEMAK:
            case self::CONFIG_PAKGEGEMAK_BE_DOMESTIC:
                return $this->retrieveAlternativeCode($configKey, $quoteTotal);
            case self::CONFIG_EPS:
            case self::CONFIG_GLOBALPACK:
                return $this->retrieveAlternativeCodeForCountry($configKey, $quoteTotal, $country);
            default:
                return null;
        }
    }

    public function isEnabled(string $configKey): bool
    {
        switch ($configKey) {
            case self::CONFIG_DELIVERY:
                return $this->productOptions->getUseAlternativeDefault();
            case self::CONFIG_BE:
                return $this->productOptions->getUseAlternativeBe();
            case self::CONFIG_EPS:
                return $this->productOptions->getUseAlternativeEps();
            case self::CONFIG_GLOBALPACK:
                return $this->productOptions->getUseAlternativeGlobalpack();
            case self::CONFIG_PAKGEGEMAK:
                return $this->productOptions->getUseAlternativePakjegemak();
            case self::CONFIG_PAKGEGEMAK_BE_DOMESTIC:
                return $this->productOptions->getUseAlternativePakjeGemakBeDomestic();
            default:
                return false;
        }
    }
}
