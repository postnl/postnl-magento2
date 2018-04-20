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
namespace TIG\PostNL\Config\Provider;

class Webshop extends AbstractConfigProvider
{
    const XPATH_WEBSHOP_LABEL_SIZE        = 'tig_postnl/webshop_printer/label_size';
    const XPATH_WEBSHOP_SHIPPING_DURATION = 'tig_postnl/webshop_shipping/shipping_duration';
    const XPATH_WEBSHOP_CUTOFFTIME        = 'tig_postnl/webshop_shipping/cutoff_time';
    const XPATH_WEBSHOP_SHIPMENTDAYS      = 'tig_postnl/webshop_shipping/shipment_days';

    const XPATH_TRACK_AND_TRACE_ENABLED       = 'tig_postnl/webshop_track_and_trace/email_enabled';
    const XPATH_TRACK_AND_TRACE_BCC_EMAIL     = 'tig_postnl/webshop_track_and_trace/email_bcc';
    const XPATH_TRACK_AND_TRACE_SERVICE_URL   = 'tig_postnl/webshop_track_and_trace/service_url';
    const XPATH_TRACK_AND_TRACE_MAIL_TEMPLATE = 'tig_postnl/webshop_track_and_trace/template';

    const XPATH_ADVANCED_ALLOWED_METHODS      = 'tig_postnl/webshop_advanced/allowed_shipping_methods';

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
    public function getShippingDuration()
    {
        return $this->getConfigFromXpath(self::XPATH_WEBSHOP_SHIPPING_DURATION);
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
    public function getShipmentDays()
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
}
