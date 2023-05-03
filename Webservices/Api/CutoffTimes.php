<?php

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
