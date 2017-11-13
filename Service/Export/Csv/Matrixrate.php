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
