<?php

namespace TIG\PostNL\Service\MatrixGrid;

use TIG\PostNL\Service\Validation\Factory;

class ErrorHandler
{
    /** @var array  */
    private $errors = [];

    /** @var Factory  */
    private $validation;

    /**
     * @param Factory        $validation
     */
    public function __construct(
        Factory $validation
    ) {
        $this->validation = $validation;
    }

    /**
     * Validate the data and return it in array format. The total of the method was too long. We can move
     * all validation to separate methods, but it will lose a lot of readability. That's why we ignore
     * the coding standards.
     *
     * @param $data
     * @param $countryCode
     * @return bool|array
     *
     * @throws \TIG\PostNL\Exception
     */
    // @codingStandardsIgnoreStart
    public function process($data, $countryCode)
    {
        // create array for validation of the region
        $regionValidationArray = [ 'country' => $countryCode, 'region'=> $data['destiny_region_id']];

        // TODO add correct Data to show within the error message
        if (!is_array($data) || empty($data)) {
            $this->errors[] = __('Invalid PostNL matrix rates format in row #%s', $data);
            return false;
        }

        if (($country = $this->validation->validate('country', $countryCode, $data['website_id'])) === false) {
            $this->errors[] = __('Invalid country "%1".', $countryCode);
            return false;
        }

        if (($region = $this->validation->validate('region', $regionValidationArray)) === false) {
            $this->errors[] = __('Invalid region/state "%1".', $regionValidationArray['region']);
            return false;
        }

        if (($weight = $this->validation->validate('weight', $data['weight'])) === false) {
            $this->errors[] = __('Invalid weight "%1".', $data['weight'], $data);
            return false;
        }

        if (($subtotal = $this->validation->validate('subtotal', $data['subtotal'])) === false) {
            $this->errors[] = __('Invalid subtotal "%1".', $data['subtotal'], $data);
            return false;
        }

        if (($quantity = $this->validation->validate('quantity', $data['quantity'])) === false) {
            $this->errors[] = __('Invalid quantity "%1".', $data['quantity'], $data);
            return false;
        }

        if (($parcelType = $this->validation->validate('parcel-type', $data['parcel_type'])) === false) {
            $this->errors[] = __(
                'Invalid parcel type "%1".',
                $data['parcel_type'],
            );
            return false;
        }

        if (($price = $this->validation->validate('price', $data['price'])) === false) {
            $this->errors[] = __('Invalid shipping price "%1" in row #%2.', $data['price'], $data);
            return false;
        }

        return [
            'website_id'         => $data['website_id'],
            'destiny_country_id' => $country,
            'destiny_region_id'  => $region,
            'destiny_zip_code'   => $data['destiny_zip_code'],
            'weight'             => $weight,
            'subtotal'           => $subtotal,
            'quantity'           => $quantity,
            'parcel_type'        => $parcelType,
            'price'              => $price,
        ];
    }
    // @codingStandardsIgnoreEnd

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) !== 0;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
