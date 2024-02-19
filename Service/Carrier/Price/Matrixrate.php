<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Tax\Helper\Data;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;

class Matrixrate
{
    /**
     * @var string
     */
    private $parcelType;

    /**
     * @var RateRequest
     */
    private $request;

    /**
     * @var Collection
     */
    private $matrixrateCollection;

    /**
     * @var Filter\CountryFilter
     */
    private $countryFilter;

    /**
     * @var Data
     */
    private $taxHelper;

    /**
     * @var boolean
     */
    private $shippingVatEnabled;

    /**
     * Matrixrate constructor.
     *
     * @param Collection           $matrixrateCollection
     * @param Filter\CountryFilter $countryFilter
     * @param Data                 $taxHelper
     */
    public function __construct(
        Collection           $matrixrateCollection,
        Filter\CountryFilter $countryFilter,
        Data                 $taxHelper
    ) {
        $this->matrixrateCollection = $matrixrateCollection;
        $this->countryFilter        = $countryFilter;
        $this->taxHelper            = $taxHelper;
    }

    /**
     * @param RateRequest $request
     * @param             $parcelType
     * @param int|null    $store
     *
     * @return array|bool
     */
    public function getRate(RateRequest $request, $parcelType, $store = null)
    {
        $matrixrateCollection     = $this->matrixrateCollection
            ->addOrder('subtotal', 'DESC')
            // Country filter should go before weight, so we filter specific countries before all (ID: 0)
            ->addOrder('destiny_country_id', 'DESC')
            ->addOrder('weight', 'DESC');
        $this->shippingVatEnabled = $this->taxHelper->shippingPriceIncludesTax($store);
        $parcelType               = $parcelType ?: 'regular';
        $collection               = $matrixrateCollection->toArray();
        $this->parcelType         = $parcelType;
        $this->request            = $request;
        $data                     = $this->filterData($collection['items']);

        if (empty($data)) {
            return false;
        }

        $result = array_shift($data);
        $result = $this->handleVat($result);

        return ['price' => $result['price'], 'cost'  => 0];
    }

    /**
     * Used to include the vat in the timeframes and locations call
     *
     * @param array    $result
     *
     * @return mixed
     */
    private function handleVat($result)
    {
        if (!$this->shippingVatEnabled) {
            return $result;
        }

        $result['price'] = $this->taxHelper->getShippingPrice($result['price'], true);

        return $result;
    }

    /**
     * Filter the data by the various available fields. The preferred way would be to do this using the database, but
     * with using SearchCriteriaFilters this is not possible. We need to complex filters to get our results, and
     * Magento does not support them. Example; ((country = 'NL' OR country = '*') AND ('region' = 157 OR region = '*')
     *
     * @param $data
     *
     * @return array
     */
    private function filterData($data)
    {
        $data = $this->countryFilter->filter($this->request, $data);
        $data = array_filter($data, [$this, 'byWebsite']);
        $data = array_filter($data, [$this, 'byWeight']);
        $data = array_filter($data, [$this, 'bySubtotal']);
        $data = array_filter($data, [$this, 'byQuantity']);
        $data = array_filter($data, [$this, 'byParcelType']);

        return $data;
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function byWebsite($row)
    {
        return $row['website_id'] == $this->request->getWebsiteId();
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function byWeight($row)
    {
        return $row['weight'] <= $this->request->getPackageWeight();
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function bySubtotal($row)
    {
        $subtotal = $this->request->getBaseSubtotalInclTax();

        if (!$subtotal) {
            $subtotal = $this->request->getPackageValue();
        }

        return $row['subtotal'] <= $subtotal;
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function byQuantity($row)
    {
        return $row['quantity'] <= $this->request->getPackageQty();
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function byParcelType($row)
    {
        return $row['parcel_type'] == $this->parcelType || $row['parcel_type'] == '*';
    }
}
