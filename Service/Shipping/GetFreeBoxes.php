<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Quote\Model\Quote\Address\RateRequest;

class GetFreeBoxes
{
    /**
     * @param RateRequest $request
     *
     * @return int
     */
    public function get(RateRequest $request)
    {
        $freeBoxes = 0;

        $allItems = $request->getAllItems();

        if (!$allItems) {
            return $freeBoxes;
        }

        foreach ($allItems as $item) {
            $freeBoxes += $this->getFreeBoxesToAdd($item);
        }

        return $freeBoxes;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return int
     */
    private function getFreeBoxesToAdd($item)
    {
        $boxesCountToAdd = 0;

        if ($item->getHasChildren() && $item->isShipSeparately()) {
            $boxesCountToAdd = $this->getFreeBoxesCountFromChildren($item);
        }

        if ($item->getFreeShipping()) {
            $boxesCountToAdd = $item->getQty() - $item->getFreeShipping();
        }

        $itemProduct = $item->getProduct();

        if ($itemProduct->isVirtual() || $item->getParentItem()) {
            $boxesCountToAdd = 0;
        }

        return $boxesCountToAdd;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return int
     */
    private function getFreeBoxesCountFromChildren($item)
    {
        $children = $item->getChildren();
        $freeChildBoxes = 0;

        array_walk(
            $children,
            function ($child) use (&$freeChildBoxes, $item) {
                /** @var \Magento\Quote\Model\Quote\Item\AbstractItem $child */
                $childProduct = $child->getProduct();

                if ($child->getFreeShipping() && !$childProduct->isVirtual()) {
                    $freeChildBoxes += $item->getQty() * ($child->getQty() - $child->getFreeShipping());
                }
            }
        );

        return $freeChildBoxes;
    }
}
