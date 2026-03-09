<?php
namespace TIG\PostNL\Service\Customer;

use Magento\Customer\Helper\Address;

class Data
{
    private const ADDRESS_MAX_LINES = 3;

    protected int $addressLineExtend = 0;

    protected Address $addressHelper;

    public function __construct(
        Address $addressHelper
    ) {
        $this->addressHelper = $addressHelper;
    }

    public function getAddressLinesExtendCount(): int
    {
        return $this->addressLineExtend;
    }

    public function canExtendAddressLines(): bool
    {
        $allowedLines = (int)$this->addressHelper->getStreetLines();
        return $allowedLines < self::ADDRESS_MAX_LINES;
    }

    public function setAddressLineExtend(): void
    {
        if (!$this->canExtendAddressLines()) {
            return;
        }

        $this->addressLineExtend = self::ADDRESS_MAX_LINES - (int)$this->addressHelper->getStreetLines();
    }
}
