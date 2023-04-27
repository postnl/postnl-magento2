<?php

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
