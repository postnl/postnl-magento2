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
namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Config\Provider\Webshop;

class CutoffTimes
{
    /** @var  Webshop */
    private $webshopSettings;

    /**
     * @param Webshop $webshopSettings
     */
    public function __construct(
        Webshop $webshopSettings
    ) {
        $this->webshopSettings = $webshopSettings;
    }

    /**
     * @return array
     */
    public function get()
    {
        return array_map(function ($value) {
            $day = (string) $value;
            return [
                'Day'  => '0'.$day,
                'Time' => $this->webshopSettings->getCutOffTimeForDay($day),
                'Available' => $this->isAvailable($day),
            ];
        }, range(1, 7));
    }

    /**
     * @return bool
     */
    public function isSundaySortingAllowed()
    {
        $shipmentDays = explode(',', $this->webshopSettings->getShipmentDays());
        return !empty([array_intersect(['6','0', '7'], $shipmentDays)]) ? 'true' : 'false';
    }

    /**
     * @param $day
     *
     * @return string
     */
    private function isAvailable($day)
    {
        $shipmentDays = explode(',', $this->webshopSettings->getShipmentDays());
        $day = $day == '7' ? '0' : $day;
        return in_array($day, $shipmentDays) ? '1' : '0';
    }
}
