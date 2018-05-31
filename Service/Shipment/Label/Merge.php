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
     * @param \TIG\PostNL\Service\Pdf\Fpdi[] $labels
     * @codingStandardsIgnoreStart
     * @param bool $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                           This parameter indicates whether to reuse the existing label PDF
     *                           @TODO Refactor to a cleaner way rather than chaining all the way to \TIG\PostNL\Service\Shipment\Label\Merge\AbstractMerger
     * @codingStandardsIgnoreEnd
     *
     * @return string
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
            $result = $this->a6Merger->files($labels, $createNewPdf);
            $output = $result->Output('s');
        }

        return $output;
    }
}
