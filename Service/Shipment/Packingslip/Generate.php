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
namespace TIG\PostNL\Service\Shipment\Packingslip;

use TIG\PostNL\Service\Pdf\Fpdi;
use TIG\PostNL\Service\Pdf\FpdiFactory;
use TIG\PostNL\Service\Shipment\Label\File;

class Generate
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var FpdiFactory
     */
    // @codingStandardsIgnoreLine
    protected $fpdi;


    public function __construct(
        File $file,
        FpdiFactory $fpdi
    ) {
        $this->file = $file;
        $this->fpdi = $fpdi;
    }

    /**
     * @param array $labels
     *
     * @return FPDI
     */
    public function run(array $labels)
    {
        $pdf = $this->fpdi->create();

        foreach ($labels as $label) {
            $filename = $this->file->save($label);

            $pdf->AddPage('P', 'A4');
            $pdf->setSourceFile($filename);
            $pageId = $pdf->importPage(1);
            $pdf->useTemplate($pageId, 0, 0);
        }

        $this->file->cleanup();

        return $pdf->Output('s');
    }
}
