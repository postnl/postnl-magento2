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

namespace TIG\PostNL\Service\Carrier\Price\Filter;

use Magento\Quote\Model\Quote\Address\RateRequest;

class CountryFilter
{
    /**
     * @var RateRequest
     */
    private $request;

    /**
     * @param RateRequest $request
     * @param array       $data
     *
     * @return array
     */
    public function filter(RateRequest $request, $data = [])
    {
        $this->request = $request;

        return array_filter($data, [$this, 'filterData']);
    }

    /**
     * @param $row
     *
     * @return bool
     */
    private function filterData($row)
    {
        $rowCountry    = $row['destiny_country_id'];
        $rowRegion     = $row['destiny_region_id'];
        $rowZipCode    = $this->normalizeZipCode($row['destiny_zip_code']) ?: '*';
        $rowHasCountry = strpos($rowCountry, $this->request->getDestCountryId()) !== false;
        $rowHasRegion  = $rowRegion == $this->request->getDestRegionId();
        $rowHasZipCode = $rowZipCode == $this->normalizeZipCode($this->request->getDestPostcode());

        return
            ($rowHasCountry && $rowHasRegion && $rowHasZipCode) ||
            ($rowHasCountry && $rowHasRegion && $rowZipCode == '*') ||
            ($rowHasCountry && $rowRegion == '0' && $rowZipCode == '*') ||
            ($rowCountry == '0' && $rowHasRegion && $rowZipCode == '*') ||
            ($rowCountry == '0' && $rowRegion == '0' && $rowZipCode == '*')
        ;
    }

    /**
     * @param $getDestPostcode
     *
     * @return mixed
     */
    private function normalizeZipCode($getDestPostcode)
    {
        return str_replace(' ', '', $getDestPostcode);
    }
}
