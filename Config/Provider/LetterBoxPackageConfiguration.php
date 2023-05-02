<?php

namespace TIG\PostNL\Config\Provider;

class LetterBoxPackageConfiguration extends AbstractConfigProvider
{
    const XPATH_CALCULATION_MODE = 'tig_postnl/letterbox_package/letterbox_package_calculation_mode';

    public function getLetterBoxPackageCalculationMode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CALCULATION_MODE, $storeId);
    }
}
