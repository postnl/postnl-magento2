<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;

class Matrixrate
{
    /**
     * @var array
     */
    private $data = [];

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
     * Matrixrate constructor.
     *
     * @param Collection           $matrixrateCollection
     * @param Filter\CountryFilter $countryFilter
     */
    public function __construct(
        Collection $matrixrateCollection,
        Filter\CountryFilter $countryFilter
    ) {
        $this->matrixrateCollection = $matrixrateCollection;
        $this->countryFilter = $countryFilter;
    }

    /**
     * @param RateRequest $request
     * @param             $parcelType
     *
     * @return array|bool
     */
    public function getRate(RateRequest $request, $parcelType)
    {
        $parcelType = $parcelType ?: 'regular';

        $collection       = $this->matrixrateCollection->toArray();
        $this->parcelType = $parcelType;
        $this->request    = $request;
        $this->data       = $collection['items'];

        $this->filterData();

        if (empty($this->data)) {
            return false;
        }

        $result = array_shift($this->data);

        return [
            'price' => $result['price'],
            'cost' => 0,
        ];
    }

    /**
     * Filter the data by the various available fields. The preferred way would be to do this using the database, but
     * with using SearchCriteriaFilters this is not possible. We need to complex filters to get our results, and
     * Magento does not support them. Example; ((country = 'NL' OR country = '*') AND ('region' = 157 OR region = '*')
     */
    private function filterData()
    {
        $this->data = $this->countryFilter->filter($this->request, $this->data);

        $this->data = array_filter($this->data, [$this, 'byWebsite']);
        $this->data = array_filter($this->data, [$this, 'byWeight']);
        $this->data = array_filter($this->data, [$this, 'bySubtotal']);
        $this->data = array_filter($this->data, [$this, 'byQuantity']);
        $this->data = array_filter($this->data, [$this, 'byParcelType']);
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
        return $row['subtotal'] <= $this->request->getPackageValue();
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
