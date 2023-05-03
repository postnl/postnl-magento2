<?php

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
        $requestRegion = $this->request->getDestRegionId() ?: 0;
        $rowHasRegion  = $rowRegion == $requestRegion;
        $rowHasZipCode = $this->hasRowZipCode($this->request->getDestPostcode(), $rowZipCode);

        return
            ($rowHasCountry && $rowHasRegion && $rowHasZipCode) ||
            ($rowHasCountry && $rowHasRegion && $rowZipCode == '*') ||
            ($rowHasCountry && $rowRegion == '0' && $rowZipCode == '*') ||
            ($rowCountry == '0' && $rowHasRegion && $rowZipCode == '*') ||
            ($rowCountry == '0' && $rowRegion == '0' && $rowZipCode == '*')
        ;
    }

    /**
     * @param $destPostcode
     * @param $rowPostcode
     *
     * @return bool
     */
    private function hasRowZipCode($destPostcode, $rowPostcode)
    {
        $destPostcode = $this->normalizeZipCode($destPostcode);
        $destPostcode = substr($destPostcode, 0, strlen($rowPostcode));

        return $rowPostcode == $destPostcode;
    }

    /**
     * @param string $postcode
     *
     * @return string
     */
    private function normalizeZipCode($postcode)
    {
        return str_replace(' ', '', (string)$postcode);
    }
}
