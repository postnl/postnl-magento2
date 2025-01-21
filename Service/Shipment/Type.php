<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment as MagentoShipment;

class Type
{

    public function get(ShipmentInterface $postNLShipment): string
    {
        // Try to get Shipment type
        $shipmentType = $this->getShipmentTypeByCode($postNLShipment);
        if (!$shipmentType) $shipmentType = $postNLShipment->getShipmentType();
        if ($shipmentType !== null) {
            return $shipmentType;
        }

        /** @var MagentoShipment $magentoShipment */
        $magentoShipment = $postNLShipment->getShipment();
        $address         = $magentoShipment->getShippingAddress();
        $countryId       = $address->getCountryId();

        return $this->getTypeForCountry($countryId);
    }

    protected function getTypeForCountry(string $countryId): string
    {
        if ($countryId === 'NL') {
            return 'Daytime';
        }

        if (in_array($countryId, EpsCountries::ALL, true)) {
            return 'EPS';
        }

        return 'GLOBALPACK';
    }

    protected function getShipmentTypeByCode(ShipmentInterface $postNLShipment): ?string
    {
        switch (true) {
            case $postNLShipment->isBoxablePackets():
                return 'boxable_packets';
            case $postNLShipment->isInternationalPacket():
                return 'priority_options';
            case $postNLShipment->isGlobalPack():
                return 'gp';
        }
        return null;
    }
}
