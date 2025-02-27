<?php
namespace TIG\PostNL\Config\Provider;

use TIG\PostNL\Api\Data\ShipmentInterface;
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

    public function getPrinterType(ShipmentInterface $shipment): string
    {
        $labelType = $this->getLabelType();
        // Just in case nothing is selected for some reason and data is not taken from the configs.
        if (!$labelType) {
            $labelType = LabelTypeSettings::TYPE_PDF;
        }
        // Smart returns, ERS should only be in PDFs
        if ($shipment->getIsSmartReturn() > 0) {
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
