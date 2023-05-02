<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Quote\Model\Quote\Address\RateRequest;

use TIG\PostNL\Model\ResourceModel\Tablerate as TablerateModel;
use TIG\PostNL\Model\ResourceModel\TablerateFactory;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;

class Tablerate
{
    /**
     * @var TablerateFactory
     */
    private $tablerateFactory;

    /**
     * @var GetFreeBoxes
     */
    private $getFreeBoxes;

    /**
     * @param TablerateFactory $tablerateFactory
     * @param GetFreeBoxes     $getFreeBoxes
     */
    public function __construct(
        TablerateFactory $tablerateFactory,
        GetFreeBoxes $getFreeBoxes
    ) {
        $this->tablerateFactory = $tablerateFactory;
        $this->getFreeBoxes = $getFreeBoxes;
    }

    /**
     * @param RateRequest $request
     * @param             $includeVirtualPrice
     *
     * @return array|bool
     */
    public function getTableratePrice(RateRequest $request, $includeVirtualPrice)
    {
        $allRequestItems = $request->getAllItems();

        if (!$includeVirtualPrice && $allRequestItems) {
            $request = $this->filterVirtualProducts($request);
        }

        $request = $this->filterFreePackages($request);

        $rate = $this->getRate($request);

        return $rate;
    }

    /**
     * @param RateRequest $request
     *
     * @return RateRequest
     */
    private function filterVirtualProducts(RateRequest $request)
    {
        $allRequestItems = $request->getAllItems();

        array_walk(
            $allRequestItems,
            function ($item) use (&$request) {
                /** @var \Magento\Quote\Model\Quote\Item $item */
                if (!$item->getParentItem()) {
                    $newPackageValue = $request->getPackageValue() - $this->getVirtualItemRowTotal($item);
                    $request->setPackageValue($newPackageValue);
                }
            }
        );

        return $request;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     *
     * @return int
     */
    private function getVirtualItemRowTotal($item)
    {
        $itemRowTotal = 0;
        $itemProduct = $item->getProduct();

        if ($itemProduct->isVirtual()) {
            $itemRowTotal = $item->getBaseRowTotal();
        }

        if ($item->getHasChildren() && $item->isShipSeparately()) {
            $itemRowTotal = 0;
            $itemChildren = $item->getChildren();
            array_walk(
                $itemChildren,
                function ($child) use (&$itemRowTotal) {
                    $itemRowTotal += $this->getVirtualItemRowTotal($child);
                }
            );
        }

        return $itemRowTotal;
    }

    /**
     * @param RateRequest $request
     *
     * @return RateRequest
     */
    private function filterFreePackages(RateRequest $request)
    {
        $freePackages = 0;
        $requestItems = $request->getAllItems();

        if (!$requestItems) {
            return $request;
        }

        array_walk(
            $requestItems,
            function ($item) use (&$freePackages) {
                $itemProduct = $item->getProduct();
                if (!$itemProduct->isVirtual() && !$item->getParentItem() && $item->getFreeShipping()) {
                    $freePackages += $item->getBaseRowTotal();
                }
            }
        );

        $request->setPackageValue($request->getPackageValue() - $freePackages);
        return $request;
    }

    /**
     * @param RateRequest $request
     *
     * @return array|bool
     */
    private function getRate(RateRequest $request)
    {
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight() ?: $oldWeight);
        $request->setPackageQty($oldQty - $this->getFreeBoxes->get($request));

        /** @var TablerateModel $tablerate */
        $tablerate = $this->tablerateFactory->create();
        $rate = $tablerate->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        return $rate;
    }
}
