<?php

namespace TIG\PostNL\Config\Provider;

/**
 * This class contains all configuration options related to webshop options.
 * This will cause that it is too long for Code Sniffer to check.
 *
 * @codingStandardsIgnoreStart
 */
class Webshop extends AbstractConfigProvider
{

    const XPATH_WEBSHOP_LABEL_SIZE          = 'tig_postnl/extra_settings_printer/label_size';
    const XPATH_WEBSHOP_LABEL_RESPONSE      = 'tig_postnl/extra_settings_printer/label_response';
    const XPATH_WEBSHOP_CUTOFFTIME          = 'tig_postnl/postnl_settings/cutoff_time';
    const XPATH_WEBSHOP_SATURDAY_CUTOFFTIME = 'tig_postnl/postnl_settings/saturday_cutoff_time';
    const XPATH_WEBSHOP_SUNDAY_CUTOFFTIME   = 'tig_postnl/postnl_settings/sunday_cutoff_time';
    const XPATH_WEBSHOP_SHIPMENTDAYS        = 'tig_postnl/postnl_settings/shipment_days';
    const XPATH_WEBSHOP_SHIPPING_DURATION   = 'tig_postnl/postnl_settings/shipping_duration';

    const XPATH_TRACK_AND_TRACE_ENABLED       = 'tig_postnl/track_and_trace/email_enabled';
    const XPATH_TRACK_AND_TRACE_BCC_EMAIL     = 'tig_postnl/track_and_trace/email_bcc';
    const XPATH_TRACK_AND_TRACE_SERVICE_URL   = 'tig_postnl/track_and_trace/service_url';
    const XPATH_TRACK_AND_TRACE_MAIL_TEMPLATE = 'tig_postnl/track_and_trace/template';

    const XPATH_ADVANCED_ALLOWED_METHODS      = 'tig_postnl/extra_settings_advanced/allowed_shipping_methods';
    const XPATH_SHOW_GRID_TOOLBAR             = 'tig_postnl/extra_settings_advanced/show_grid_toolbar';

    const XPATH_ADDRESS_CHECK_ENABLED         = 'tig_postnl/addresscheck/enable_postcodecheck';
    const XPATH_ADDRESS_CHECK_COMPATIBLE      = 'tig_postnl/addresscheck/checkout_compatible';

    const XPATH_INTERNATIONAL_ADDRESS_ENABLED = 'tig_postnl/internationaladdressoptions/enable';

    const XPATH_POSTCODE_ADDRESS_CHECK_ENABLED = 'tig_postcode/configuration/modus';

    const XPATH_CLEAR_OLD_SHIPMENT_LABELS = 'tig_postnl/labelandpackingslipoptions/enable_expired_label_cleanup';

    /**
     * @return bool
     */
    public function getIsInternationalAddressEnabled()
    {
        return $this->getConfigFromXpath(self::XPATH_INTERNATIONAL_ADDRESS_ENABLED);
    }

    /**
     * @return bool
     */
    public function getIsAddressCheckEnabled()
    {
        if ($this->getConfigFromXpath(self::XPATH_POSTCODE_ADDRESS_CHECK_ENABLED)) {
            return false;
        }

        return $this->getConfigFromXpath(self::XPATH_ADDRESS_CHECK_ENABLED);
    }

    /**
     * @return string
     */
    public function getCheckoutCompatibleForAddressCheck()
    {
        return $this->getConfigFromXpath(self::XPATH_ADDRESS_CHECK_COMPATIBLE);
    }

    /**
     * @return mixed
     */
    public function getLabelSize()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_LABEL_SIZE);
    }

    /**
     * @return mixed
     */
    public function getLabelResponse()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_LABEL_RESPONSE);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getShippingDuration($storeId = null)
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_SHIPPING_DURATION, $storeId);
    }

    /**
     * @return mixed
     */
    public function getCutOffTime()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_CUTOFFTIME);
    }

    /**
     * @return mixed
     */
    public function getSaturdayCutOffTime()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_SATURDAY_CUTOFFTIME);
    }

    /**
     * @return mixed
     */
    public function getSundayCutOffTime()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_SUNDAY_CUTOFFTIME);
    }

    /**
     * @param $day
     *
     * @return mixed
     */
    public function getCutOffTimeForDay($day)
    {
        switch ($day) {
            case '7':
                return $this->getSundayCutOffTime();
            case '6':
                return $this->getSaturdayCutOffTime();
            default:
                return $this->getCutOffTime();
        }
    }

    /**
     * @return mixed
     */
    public function getShipmentDays(): string
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_SHIPMENTDAYS);
    }

    /**
     * @return mixed
     */
    public function isTrackAndTraceEnabled()
    {
        return $this->getConfigFromXpath(self::XPATH_TRACK_AND_TRACE_ENABLED);
    }

    /**
     * @return mixed
     */
    public function getTrackAndTraceServiceUrl()
    {
        return $this->getConfigFromXpath(self::XPATH_TRACK_AND_TRACE_SERVICE_URL);
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getTrackAndTraceEmailTemplate($storeId = null)
    {
        return $this->getConfigFromXpath(self::XPATH_TRACK_AND_TRACE_MAIL_TEMPLATE, $storeId);
    }

    /**
     * @param null|int $storeId
     *
     * @return mixed
     */
    public function getTrackAndTraceBccEmail($storeId = null)
    {
        return $this->getConfigFromXpath(self::XPATH_TRACK_AND_TRACE_BCC_EMAIL, $storeId);
    }

    /**
     * @param null|int $storeId
     *
     * @return array
     */
    public function getAllowedShippingMethods($storeId = null)
    {
        $shippingMethods = $this->getConfigFromXpath(self::XPATH_ADVANCED_ALLOWED_METHODS, $storeId);
        if (!$shippingMethods) {
            return [];
        }

        return explode(',', $shippingMethods);
    }

    /**
     * @param null|int $storeId
     *
     * @return bool
     */
    public function getShowToolbar($storeId = null)
    {
        return $this->getConfigFromXpath(self::XPATH_SHOW_GRID_TOOLBAR, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isExpiredLabelCleanupEnabled(int $storeId = null): bool
    {
        return (bool)$this->getConfigFromXpath(self::XPATH_CLEAR_OLD_SHIPMENT_LABELS, $storeId);
    }
}
