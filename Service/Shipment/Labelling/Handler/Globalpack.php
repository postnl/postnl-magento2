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
namespace TIG\PostNL\Service\Shipment\Labelling\Handler;

/**
 * Globalpack returns in some cases three kind of labels. CN23, CN71 and a commercial invoice.
 * This handler will merge them togetter so it is stored as one inside the tig_postnl_shipment_label table.
 *
 * @package TIG\PostNL\Service\Shipment\Labelling\Handler
 */
class Globalpack extends AbstractHandler implements HandlerInterface
{
    const LABEL_CN23_TYPE       = 'CN23';
    const LABEL_CP71_TYPE       = 'CP71';
    const LABEL_COMMERCIAL_TYPE = 'CommercialInvoice';

    /**
     * @param object $labelItems
     *
     * @return string
     */
    public function format($labelItems)
    {
        $this->pdf = $this->fpdiFactory->create();
        foreach ($labelItems as $labelItem) {
            $this->handleContent($labelItem);
        }

        $this->cleanup();

        return $this->pdf->Output('S');
    }

    /**
     * Handle all pages of the label content.
     * @param $labelItem
     */
    private function handleContent($labelItem)
    {
        $tempLabel = $this->getTempLabel($labelItem->Content);
        $this->pdf->setSourceFile($tempLabel);

        $this->addLabelToPdf($labelItem->Labeltype);
    }

    /**
     * Page 1 and 2 are the CN23 and CN71 labels, these fitt on one A4.
     * @param $type
     */
    private function addLabelToPdf($type)
    {
        if ($type !== static::LABEL_CP71_TYPE) {
            $templateId  = $this->pdf->importPage(1);
            $this->pdf->AddPage('P', 'A4');
            $this->pdf->useTemplate($templateId, 3.9, 4.5, 204.2);
        }

        if ($type == static::LABEL_CP71_TYPE) {
            $templateId  = $this->pdf->importPage(1);
            $this->pdf->useTemplate($templateId, 3.9, 152.1, 204.2);
        }
    }
}
