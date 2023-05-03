<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;

class Type
{
    /**
     * @param ShipmentInterface $postNLShipment
     *
     * @return null|string
     */
    public function get(ShipmentInterface $postNLShipment)
    {
        $shipmentType = $postNLShipment->getShipmentType();
        if ($shipmentType !== null) {
            return $shipmentType;
        }

        /** @var MagentoShipment $magentoShipment */
        $magentoShipment = $postNLShipment->getShipment();
        $address         = $magentoShipment->getShippingAddress();
        $countryId       = $address->getCountryId();

        return $this->getTypeForCountry($countryId);
    }

    /**
     * @param string $countryId
     *
     * @return string
     */
    private function getTypeForCountry($countryId)
    {
        if ($countryId == 'NL') {
            return 'Daytime';
        }

        if (in_array($countryId, EpsCountries::ALL)) {
            return 'EPS';
        }

        return 'GLOBALPACK';
    }
}
