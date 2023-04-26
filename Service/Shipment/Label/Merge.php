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

use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Service\Pdf\Fpdi;

class Merge
{
    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @var Merge\A4Merger
     */
    private $a4Merger;

    /**
     * @var Merge\A6Merger
     */
    private $a6Merger;

    /**
     * @param Webshop        $webshopConfiguration
     * @param Merge\A4Merger $a4Merger
     * @param Merge\A6Merger $a6Merger
     * @param File           $file
     */
    public function __construct(
        Webshop $webshopConfiguration,
        Merge\A4Merger $a4Merger,
        Merge\A6Merger $a6Merger
    ) {
        $this->webshop = $webshopConfiguration;
        $this->a4Merger = $a4Merger;
        $this->a6Merger = $a6Merger;
    }

    /**
     * Some labels simply don't fit on an A6 (e.g. Globalpack labels).
     * Instead of simply blocking these, we'll print them as A4s.
     *
     * @param Fpdi[] $labels
     * @param bool $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                           This parameter indicates whether to reuse the existing label PDF
     *
     * @TODO Avoid chaining to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     *
     * @return string
     * @throws \Exception
     */
    public function files(array $labels, $createNewPdf = false)
    {
        $output = '';
        if ($this->webshop->getLabelSize() == 'A4' || $createNewPdf) {
            $result = $this->a4Merger->files($labels, $createNewPdf);
            $output = $result->Output('s');
        }

        //  Create PDF is used for packingslips which are always A4.
        if ($this->webshop->getLabelSize() == 'A6' && !$createNewPdf) {
            $result = $this->mergeA6Labels($labels, $createNewPdf);
            $output = $result->Output('s');
        }

        return $output;
    }

    /**
     * @param Fpdi[] $labels
     * @param bool   $createNewPdf
     *
     * @return Fpdi
     * @throws \Exception
     */
    private function mergeA6Labels($labels, $createNewPdf)
    {
        $a4Labels = $this->getGPlabels($labels);
        $a6Labels = $this->getNonGPlabels($labels);

        $a4result = $this->a4Merger->files($a4Labels, $createNewPdf);

        /** There might be no A6 labels, i.e. when only GP labels are printed.
         * Concatting an empty pdf at the end of an pdf will result in adding an empty page,
         * therefore check if there are A6 labels to merge.
         */
        if (!empty($a6Labels)) {
            $a6result = $this->a6Merger->files($a6Labels, true);
            $a4result->concatPdf($a6result);
        }

        return $a4result;
    }

    /**
     * @param $labels
     *
     * @return array
     */
    private function getGPlabels($labels)
    {
        return array_filter($labels, function ($label) {
            return $label->shipmentType == 'GP';
        });
    }

    /**
     * @param $labels
     *
     * @return array
     */
    private function getNonGPlabels($labels)
    {
        return array_filter($labels, function ($label) {
            return $label->shipmentType != 'GP';
        });
    }
}
