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

use TIG\PostNL\Config\Source\Settings\LabelTypeSettings;

class PrintSettingsConfiguration extends AbstractConfigProvider
{
    const XPATH_PRINT_SETTINGS_LABEL_SIZE     = 'tig_postnl/extra_settings_printer/label_size';
    const XPATH_PRINT_SETTINGS_LABEL_TYPE     = 'tig_postnl/extra_settings_printer/label_type';
    const XPATH_PRINT_SETTINGS_LABEL_DPI      = 'tig_postnl/extra_settings_printer/label_dpi';
    const XPATH_PRINT_SETTINGS_LABEL_RESPONSE = 'tig_postnl/extra_settings_printer/label_response';

    public function getLabelSize(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_PRINT_SETTINGS_LABEL_SIZE);
    }

    public function getLabelType(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_PRINT_SETTINGS_LABEL_TYPE);
    }

    public function getLabelDpi(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_PRINT_SETTINGS_LABEL_DPI);
    }

    public function getLabelResponse(): string
    {
        return (string)$this->getConfigFromXpath(self::XPATH_PRINT_SETTINGS_LABEL_RESPONSE);
    }

    public function getPrinterType(): string
    {
        $labelType = $this->getLabelType();
        // Just in case nothing is selected for some reason and data is not taken from the configs.
        if (!$labelType) {
            $labelType = LabelTypeSettings::TYPE_PDF;
        }
        // Zebra has a different view from other types
        if ($labelType === LabelTypeSettings::TYPE_ZPL) {
            $printType = 'Zebra|Generic ZPL II';
        } else {
            $printType = 'GraphicFile|' . $labelType;
        }
        // DPI is not available only for PDF
        if ($labelType !== LabelTypeSettings::TYPE_PDF) {
            $printType .= ' ' . $this->getLabelDpi() . ' dpi';
        }
        return $printType;
    }
}
