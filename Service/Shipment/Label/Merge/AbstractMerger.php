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

namespace TIG\PostNL\Service\Shipment\Label\Merge;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

class AbstractMerger
{
    /**
     * @var Fpdi
     */
    // @codingStandardsIgnoreLine
    protected $pdf;

    /**
     * @var File
     */
    // @codingStandardsIgnoreLine
    protected $file;

    /**
     * @var FpdiFactory
     */
    // @codingStandardsIgnoreLine
    protected $fpdiFactory;

    /**
     * @param FpdiFactory $fpdiFactory
     * @param File $file
     */
    public function __construct(
        FpdiFactory $fpdiFactory,
        File $file
    ) {
        $this->fpdiFactory = $fpdiFactory;
        $this->file = $file;
    }

    /**
     * @param bool $addPage
     * @codingStandardsIgnoreStart
     * @param bool $createNewPdf Sometimes you want to generate a new Label PDF, for example when printing packingslips
     *                           This parameter indicates whether to reuse the existing label PDF
     *                           @TODO Refactor to a cleaner way rather than chaining all the way to here
     * @@codingStandardsIgnoreEnd
     *
     * @return \FPDF|mixed|null|\PDF
     */
    // @codingStandardsIgnoreLine
    protected function createPdf($addPage = false, $createNewPdf = false)
    {
        static $pdf = null;

        if ($pdf && !$createNewPdf) {
            return $pdf;
        }

        $pdf = $this->fpdiFactory->create();

        if ($addPage) {
            $pdf->addPage('L', 'A4');
        }

        return $pdf;
    }
}
