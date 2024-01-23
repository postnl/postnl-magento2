<?php
namespace TIG\PostNL\Block\Adminhtml\Grid\Filter;

use Magento\Framework\Data\OptionSourceInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;

class ReturnStatus implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = ['label' => $label, 'value' => $value];
        }
        return $result;
    }

    public function getOptions(): array
    {
        return [
            ShipmentInterface::RETURN_STATUS_DEFAULT => __('Active'),
            ShipmentInterface::RETURN_STATUS_BLOCKED => __('Blocked'),
            ShipmentInterface::RETURN_STATUS_RELEASED => __('Activated'),
        ];
    }
}
