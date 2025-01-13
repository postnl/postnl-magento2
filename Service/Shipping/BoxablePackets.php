<?php
namespace TIG\PostNL\Service\Shipping;

class BoxablePackets extends LetterboxPackage
{
    protected const ATTRIBUTE_KEY = 'postnl_max_qty_international_letterbox';

    protected function isEnabled(bool $isPossibleLetterboxPackage = false): bool
    {
        if (!$isPossibleLetterboxPackage && !$this->shippingOptions->isBoxablePacketsActive()) {
            return false;
        }

        //only when send from NL
        $senderAddressCountry = $this->addressConfiguration->getCountry();
        if ($senderAddressCountry !== 'NL') {
            return false;
        }

        return true;
    }
}
