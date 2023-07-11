<?php

namespace TIG\PostNL\Config\Provider;

class PepsConfiguration extends AbstractConfigProvider
{
    const XPATH_BARCODE_RANGE = 'tig_postnl/peps/barcode_range';

    const XPATH_CALCULATION_MODE = 'tig_postnl/peps/peps_boxable_packets_calculation_mode';

    /**
     * @param null|int|string $productCode
     *
     * @return mixed
     */
    public function getBarcodeType($productCode = null)
    {
        switch ($productCode) {
            case '6440':
            case '6405':
                $type = 'UE';
                break;
            case '6906':
                $type = 'RI';
                break;
            case '6972':
            case '6350':
            default:
                $type = 'LA';
                break;
        }

        return $type;
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
    public function getBoxablePacketCalculationMode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CALCULATION_MODE, $storeId);
    }
}
