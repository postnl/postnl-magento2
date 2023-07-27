<?php

namespace TIG\PostNL\Plugin\Customer;

use Magento\Customer\Model\Data\Customer as SubjectClass;
use TIG\PostNL\Service\Customer\Data;

class CustomerData
{
    private Data $customerDataService;

    public function __construct(
        Data $customerDataService
    ) {
        $this->customerDataService = $customerDataService;
    }

    public function beforeSetAddresses(
        SubjectClass $subject,
        array $addresses = null
    ) {
        if (!empty($addresses)) {
            $this->customerDataService->setAddressLineExtend();
        }
        return null;
    }
}
