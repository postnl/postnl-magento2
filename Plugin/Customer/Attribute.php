<?php
namespace TIG\PostNL\Plugin\Customer;

use Magento\Customer\Model\Attribute as SubjectClass;
use TIG\PostNL\Service\Customer\Data;

class Attribute
{
    public const ALLOWED_ATTRIBUTE = 'street';
    private Data $customerDataService;
    private bool $processing = false;

    public function __construct(
        Data $customerDataService
    ) {
        $this->customerDataService = $customerDataService;
    }

    /**
     * @param SubjectClass $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetMultilineCount(
        SubjectClass $subject,
        $result
    ) {
        // Check attribute, this is only valid for street one
        if ($subject->getAttributeCode() !== self::ALLOWED_ATTRIBUTE) {
            return false;
        }
        if ($this->processing) {
            return $result;
        }
        // Prevent recursion
        $this->processing = true;
        if ($this->canExtendAttributeValue($subject)) {
            $result += $this->customerDataService->getAddressLinesExtendCount();
        }
        $this->processing = false;
        return $result;
    }

    private function canExtendAttributeValue(SubjectClass $subject): bool
    {
        if ($this->customerDataService->getAddressLinesExtendCount() === 0
            || !$this->customerDataService->canExtendAddressLines()
        ) {
            return false;
        }
        return true;
    }
}
