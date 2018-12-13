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
namespace TIG\PostNL\Service\Shipment\Label\Type;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Pdf\Fpdi;

class EPS extends Domestic
{
    /**
     * These are combiLabel products, these codes are returned by PostNL in the label response (ProductCodeDelivery)
     */
    private $shouldRotate = [4950, 4983, 4985, 4986];

    /**
     * @var bool
     */
    private $templateInserted = false;

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \FPDF
     */
    public function process(ShipmentLabelInterface $label)
    {
        $filename = $this->saveTempLabel($label);

        $this->createPdf();
        $this->pdf->AddPage('P', Fpdi::PAGE_SIZE_A6);
        $this->pdf->setSourceFile($filename);

        if ($this->isRotated() || in_array($label->getProductCode(), $this->shouldRotate)) {
            $this->insertRotated();
        }

        if (!$this->templateInserted) {
            $this->insertRegular();
        }

        return $this->pdf;
    }

    /**
     * This is a label that is standing, so rotate is before pasting.
     */
    private function insertRotated()
    {
        $this->templateInserted = true;
        $pageId = $this->pdf->importPage(1);
        $this->pdf->Rotate(90);
        $this->pdf->useTemplate($pageId, - 130, 0);
        $this->pdf->Rotate(0);
    }

    /**
     * This is a default label, it does not need any modification.
     */
    private function insertRegular()
    {
        $this->templateInserted = true;
        $pageId = $this->pdf->importPage(1);
        $this->pdf->useTemplate($pageId, 0, 0, Fpdi::PAGE_SIZE_A6_WIDTH, Fpdi::PAGE_SIZE_A6_HEIGHT);
    }

    /**
     * @return bool
     */
    public function isRotated()
    {
        $pageId = $this->pdf->importPage(1);
        $sizes = $this->pdf->getTemplateSize($pageId);

        if (isset($sizes['w']) && isset($sizes['h']) && $sizes['w'] > $sizes['h']) {
            return true;
        }

        return false;
    }
}
