<?php
namespace TIG\PostNL\Service\Customer;

use Magento\Customer\Helper\Address;

class Data
{
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
        return $allowedLines === 1;
    }

    public function setAddressLineExtend(): void
    {
        if (!$this->canExtendAddressLines()) {
            return;
        }
        $this->addressLineExtend = 2;
    }
}
