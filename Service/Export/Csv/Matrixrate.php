<?php

namespace TIG\PostNL\Service\Export\Csv;

use TIG\PostNL\Api\Data\MatrixrateInterface;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;
use TIG\PostNL\Service\Converter\IdToRegion;
use TIG\PostNL\Service\Converter\ZeroToStar;

class Matrixrate
{
    /**
     * @var resource
     */
    private $output;

    /**
     * @var IdToRegion
     */
    private $idToRegion;

    /**
     * @var ZeroToStar
     */
    private $zeroToStar;

    /**
     * Matrixrate constructor.
     *
     * @param IdToRegion $idToRegion
     * @param ZeroToStar $zeroToStar
     */
    public function __construct(
        IdToRegion $idToRegion,
        ZeroToStar $zeroToStar
    ) {
        $this->idToRegion = $idToRegion;
        $this->zeroToStar = $zeroToStar;
    }

    /**
     * @param Collection $collection
     *
     * @return bool|string
     */
    public function build(Collection $collection)
    {
        $this->output = tmpfile();

        $this->addHeaders();

        foreach ($collection as $model) {
            $this->addRow($model);
        }

        rewind($this->output);

        // @codingStandardsIgnoreStart
        /**
         * This is discouraged by Magento, but there is not alternative as far as found.
         */
        $output = stream_get_contents($this->output);
        fclose($this->output);
        // @codingStandardsIgnoreEnd

        return trim($output);
    }

    /**
     * Add the headers at the top of the file.
     */
    private function addHeaders()
    {
        // @codingStandardsIgnoreStart
        $this->output([
            __('Country'),
            __('Province/state'),
            __('Zipcode'),
            __('Weight (and higher)'),
            __('Shipping price (and higher)'),
            __('Amount (and higher)'),
            __('Parcel type'),
            __('price'),
            __('Instructions'),
        ]);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @param MatrixrateInterface $model
     * @return void
     * @throws \TIG\PostNL\Exception
     */
    private function addRow(MatrixrateInterface $model)
    {
        $this->output([
            $this->zeroToStar->convert($model->getDestinyCountryId()),
            $this->idToRegion->convert($model->getDestinyRegionId()),
            $model->getDestinyZipCode(),
            $model->getWeight(),
            $model->getSubtotal(),
            $model->getQuantity(),
            $model->getParcelType(),
            $model->getPrice(),
            '',
        ]);
    }

    /**
     * @param $line
     */
    private function output($line)
    {
        fputcsv($this->output, $line);
    }
}
