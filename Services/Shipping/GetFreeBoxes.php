<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Services\Shipping;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class GetFreeBoxes
 *
 * @package TIG\PostNL\Services\Shipping
 */
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
            function ($child, $key) use (&$freeChildBoxes, $item) {
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
