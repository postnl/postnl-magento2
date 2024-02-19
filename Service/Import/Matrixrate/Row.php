<?php

namespace TIG\PostNL\Service\Import\Matrixrate;

use TIG\PostNL\Service\Validation\Factory;
use TIG\PostNL\Service\Wrapper\StoreInterface;

class Row
{
    const ALLOWED_PARCEL_TYPES = [
        'regular',
        'pakjegemak',
        'extra@home',
        'letterbox_package',
        'boxable_packets',
    ];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Factory
     */
    private $validation;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var bool
     */
    private $needsDataReset = true;

    /**
     * @param StoreInterface $store
     * @param Factory        $validation
     */
    public function __construct(
        StoreInterface $store,
        Factory        $validation
    ) {
        $this->validation = $validation;
        $this->store      = $store;
    }

    /**
     * Validate the data and return it in array format. The total of the method was too long. We can move
     * all validation to separate methods, but it will loose a lot of readability. That's why we ignore
     * the coding standards.
     *
     * @param int $row
     * @param array $line
     * @param int $website
     *
     * @return bool|array
     */
    // @codingStandardsIgnoreStart
    public function process($row, $line, $website)
    {
        $this->resetData();

        if (!is_array($line) || empty($line)) {
            $this->errors[] = __('Invalid PostNL matrix rates format in row #%s', $row);
            return false;
        }

        if (($country = $this->validation->validate('country', $line[0], $website)) === false) {
            $this->errors[] = __('Invalid country "%1" in row #%2.', $line[0], $row);
            return false;
        }

        if (($region = $this->validation->validate('region', ['country' => $line[0], 'region' => $line[1]])) === false) {
            $this->errors[] = __('Invalid region/state "%1" in row #%2.', $line[1], $row);
            return false;
        }

        if (($weight = $this->validation->validate('weight', $line[3])) === false) {
            $this->errors[] = __('Invalid weight "%1" in row #%2.', $line[3], $row);
            return false;
        }

        if (($subtotal = $this->validation->validate('subtotal', $line[4])) === false) {
            $this->errors[] = __('Invalid subtotal "%1" in row #%2.', $line[4], $row);
            return false;
        }

        if (($quantity = $this->validation->validate('quantity', $line[5])) === false) {
            $this->errors[] = __('Invalid quantity "%1" in row #%2.', $line[5], $row);
            return false;
        }

        if (($parcelType = $this->validation->validate('parcel-type', $line[6])) === false) {
            $this->errors[] = __(
                'Invalid parcel type "%1" in row #%2. Valid values are: "%3".',
                $line[6],
                $row,
                implode(', ', self::ALLOWED_PARCEL_TYPES)
            );
            return false;
        }

        if (($price = $this->validation->validate('price', $line[7])) === false) {
            $this->errors[] = __('Invalid shipping price "%1" in row #%2.', $line[7], $row);
            return false;
        }

        if ($this->validation->validate('duplicate-import', $line) === false) {
            $this->errors[] = __(
                'Duplicate row #%1 (country "%2", region/state "%3", zip "%4", weight "%5", ' .
                'subtotal "%6", quantity "%7" and parcel type "%8").',
                $row,
                $line[0],
                $line[1],
                $line[2],
                $line[3],
                $line[4],
                $line[5],
                $line[6],
                $line[7]
            );
            return false;
        }

        return [
            'website_id'         => $website,
            'destiny_country_id' => $country,
            'destiny_region_id'  => $region,
            'destiny_zip_code'   => $line[2],
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

    /**
     * The resetData() method call only called once per instance.
     */
    private function resetData()
    {
        if ($this->needsDataReset) {
            $this->needsDataReset = false;
            $this->validation->resetData();
        }
    }
}
