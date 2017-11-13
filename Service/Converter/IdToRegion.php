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

namespace TIG\PostNL\Service\Converter;

use Magento\Directory\Api\Data\RegionInformationInterface;
use Magento\Directory\Model\ResourceModel\Region\Collection;

class IdToRegion implements ContractInterface
{
    /**
     * @var Collection
     */
    private $regionCollection;

    /**
     * @var array
     */
    private $regions = [];

    /**
     * @var ZeroToStar
     */
    private $zeroToStar;

    /**
     * IdToRegion constructor.
     *
     * @param Collection $regionCollection
     * @param ZeroToStar $zeroToStar
     */
    public function __construct(
        Collection $regionCollection,
        ZeroToStar $zeroToStar
    ) {
        $this->regionCollection = $regionCollection;
        $this->zeroToStar = $zeroToStar;
    }

    /**
     * Convert the value.
     *
     * @param $value
     *
     * @return mixed
     *
     * @throws \TIG\PostNL\Exception
     */
    public function convert($value)
    {
        $value = $this->zeroToStar->convert($value);
        if ($value == '*') {
            return $value;
        }

        $region = $this->getRegion($value);
        if ($region) {
            return $region->getName();
        }

        throw new \TIG\PostNL\Exception(__('"%1" is not a valid region', $value));
    }

    /**
     * @param $value
     *
     * @return bool|RegionInformationInterface
     */
    private function getRegion($value)
    {
        $regions = $this->getRegions();

        if (array_key_exists($value, $regions)) {
            return $regions[$value];
        }

        return false;
    }

    /**
     * @return RegionInformationInterface[]
     */
    private function getRegions()
    {
        if (!empty($this->regions)) {
            return $this->regions;
        }

        /** @var RegionInformationInterface $region */
        foreach ($this->regionCollection as $region) {
            $this->regions[$region->getId()] = $region;
        }

        return $this->regions;
    }
}
