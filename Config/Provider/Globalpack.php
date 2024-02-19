<?php

namespace TIG\PostNL\Config\Provider;

/**
 * @codingStandardsIgnoreStart
 */
class Globalpack extends AbstractConfigProvider
{
    const XPATH_ENABLED            = 'tig_postnl/globalpack/enabled';
    const XPATH_BARCODE_TYPE       = 'tig_postnl/globalpack/barcode_type';
    const XPATH_BARCODE_RANGE      = 'tig_postnl/globalpack/barcode_range';
    const XPATH_LICENSE_NUMBER     = 'tig_postnl/globalpack/license_number';
    const XPATH_CERTIFICATE_NUMBER = 'tig_postnl/globalpack/certificate_number';
    const XPATH_SHIPMENT_TYPE      = 'tig_postnl/globalpack/shipment_type';

    // Customs attribute selections
    const XPATH_USE_HS_TARIFF             = 'tig_postnl/globalpack/product_settings_use_hs_tariff';
    const XPATH_HS_TARIFF_ATTRIBUTE       = 'tig_postnl/globalpack/product_settings_hs_tariff_attribute';
    const XPATH_PRODUCT_VALUE_ATTRIBUTE   = 'tig_postnl/globalpack/product_settings_value_attribute';
    const XPATH_PRODUCT_COUNTRY_OF_ORIGIN = 'tig_postnl/globalpack/product_settings_country_of_origin';
    const XPATH_PRODUCT_DESCRIPTION      = 'tig_postnl/globalpack/product_settings_description';
    const XPATH_PRODUCT_SORTING           = 'tig_postnl/globalpack/product_settings_sorting_attribute';
    const XPATH_PRODUCT_SORTING_DIRECTION = 'tig_postnl/globalpack/product_settings_sorting_direction';

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBarcodeType($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_TYPE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBarcodeRange($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_RANGE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getLicenseNumber($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_LICENSE_NUMBER, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCertificateNumber($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CERTIFICATE_NUMBER, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getDefaultShipmentType($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_SHIPMENT_TYPE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function useHsTariff($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_USE_HS_TARIFF, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getHsTariffAttributeCode($storeId = null)
    {
        if (!$this->useHsTariff($storeId)) {
            return false;
        }

        return $this->getConfigFromXpath(static::XPATH_HS_TARIFF_ATTRIBUTE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getProductValueAttributeCode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_PRODUCT_VALUE_ATTRIBUTE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getProductCountryOfOriginAttributeCode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_PRODUCT_COUNTRY_OF_ORIGIN, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getProductDescriptionAttributeCode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_PRODUCT_DESCRIPTION, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getProductSortingAttributeCode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_PRODUCT_SORTING, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getProductSortingDirection($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_PRODUCT_SORTING_DIRECTION, $storeId);
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
