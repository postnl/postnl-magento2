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
            $labelResult['label']->shipmentType = $labelResult['shipment']->getShipmentType();
            $preparedLabels[] = $labelResult['label'];
        }

        return $this->merge->files($preparedLabels, $createNewPdf);
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
