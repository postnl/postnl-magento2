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

class GlobalPack extends AbstractType implements TypeInterface
{
    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \TIG\PostNL\Service\Pdf\Fpdi
     */
    public function process(ShipmentLabelInterface $label)
    {
        $filename = $this->saveTempLabel($label);

        $this->pdf = $this->fpdi->create();
        $count = $this->pdf->setSourceFile($filename);
        for ($pageNo = 1; $pageNo <= $count; $pageNo++) {
            $templateId   = $this->pdf->importPage($pageNo);
            $templateSize = $this->pdf->getTemplateSize($templateId);
            $orientation  = $templateSize['width'] > $templateSize['height'] ? 'L' :'P';

            $this->pdf->AddPage($orientation, [$templateSize['width'], $templateSize['height']]);
            $this->pdf->useTemplate($templateId);
        }

        return $this->pdf;
    }
}
