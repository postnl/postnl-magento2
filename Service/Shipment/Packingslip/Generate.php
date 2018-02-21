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


class Generate
{
    /**
     * @var \Zend_Pdf
     */
    private $pdf;

    /**
     * @param array $labels
     *
     * @return string
     */
    public function run(array $labels)
    {
        $this->pdf = new \Zend_Pdf();

        foreach ($labels as $label) {
            $this->addLabelToPdf($label);
        }

        return $this->pdf->render();
    }

    /**
     * @param string $label
     */
    private function addLabelToPdf($label)
    {
        $labelZendPdf = \Zend_Pdf::parse($label);

        foreach ($labelZendPdf->pages as $page) {
            $this->pdf->pages[] = clone $page;
        }
    }
}
