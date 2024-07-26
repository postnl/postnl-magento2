<?php

namespace TIG\PostNL\Model\Config\Backend;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\PostNL\Service\Validation\AlternativeDelivery;

class SerializedDeliveryForCountry extends SerializedDelivery
{
    private CountryInformationAcquirerInterface $countryInformationAcquirer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data, $serializer);
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            $value = $this->validateCountry($value);
            $this->setValue($value);
        }
        return parent::beforeSave();
    }

    /**
     * @throws LocalizedException
     */
    private function validateCountry(array $value): array
    {
        $availableCountries = [];
        foreach ($value as $key => $scaleRow) {
            // Ignore __empty array
            if (!is_array($scaleRow)) {
                continue;
            }
            $country = (string)($scaleRow[AlternativeDelivery::DELIVERY_MAP_COUNTRY] ?? '');
            if (!$country || $country === '*' || strpos($country, '*') !== false) {
                // Fall back for all countries;
                $value[$key][AlternativeDelivery::DELIVERY_MAP_COUNTRY] = '*';
                continue;
            }
            // Validate each country
            $split = explode(',', $country);
            $result = [];
            foreach ($split as $countryCode) {
                $countryCode = strtoupper($countryCode);
                if (array_key_exists($countryCode, $availableCountries)) {
                    $result[] = $countryCode;
                    continue;
                }
                try {
                    $info = $this->countryInformationAcquirer->getCountryInfo($countryCode);
                    $result[] = $countryCode;
                    $availableCountries[$countryCode] = 1;
                } catch (NoSuchEntityException $e) {
                    throw new LocalizedException(__('Invalid country id in alternative options list: %1', $countryCode));
                }
            }
            $value[$key][AlternativeDelivery::DELIVERY_MAP_COUNTRY] = implode(',', $result);
        }
        return $value;
    }
}
