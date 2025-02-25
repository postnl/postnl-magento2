<?php

namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;

class Generate
{
    /**
     * @var Prepare
     */
    private $prepare;

    /**
     * @var Merge
     */
    private $merge;

    /**
     * @var array
     */
    private $globalPackLabels = [];

    /**
     * @param Prepare $prepare
     * @param Merge   $merge
     */
    public function __construct(
        Prepare $prepare,
        Merge $merge
    ) {
        $this->prepare = $prepare;
        $this->merge = $merge;
    }

    /**
     *
     *
     * @param array $labels
     *
     * @codingStandardsIgnoreStart
     *
     * @param bool  $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                            This parameter indicates whether to reuse the existing label PDF
     *
     * @TODO Refactor to a cleaner way rather than chaining all the way to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     * @codingStandardsIgnoreEnd
     *
     * @return string
     * @throws \TIG\PostNL\Exception
     */
    public function run(array $labels, $createNewPdf = false)
    {
        $preparedLabels = [];
        $this->globalPackLabels = [];

        foreach ($this->orderLabels($labels) as $label) {
            $labelResult = $this->prepare->label($label);
            $preparedLabels[] = $labelResult['label'];
        }

        if (is_object($preparedLabels[0])) {
            return $this->merge->files($preparedLabels, $createNewPdf);
        }
        return $preparedLabels[0];
    }

    /**
     * @param $labels
     *
     * @return array
     */
    public function orderLabels($labels)
    {
        $otherLabels = array_filter($labels, function ($label) {
            /** @var array|ShipmentLabelInterface $label */
            if (is_array($label)) {
                return false;
            }
            if (strtoupper((string)$label->getType()) == ProductInfo::SHIPMENT_TYPE_GP) {
                $this->globalPackLabels[] = $label;
                return false;
            }
            return true;
        });

        return array_merge($this->globalPackLabels, $otherLabels);
    }
}
