<?php

namespace TIG\PostNL\Config\Provider;

class LoggingConfiguration extends AbstractConfigProvider
{
    const XPATH_LOGGING_TYPE = 'tig_postnl/developer_settings/types';

    /**
     * @return mixed
     */
    public function getLoggingTypes()
    {
        return $this->getConfigFromXpath(self::XPATH_LOGGING_TYPE);
    }

    /**
     * @param $level
     *
     * @return bool
     */
    public function canLog($level)
    {
        $logTypes = explode(',', (string)$this->getLoggingTypes());

        return in_array($level, $logTypes);
    }
}
