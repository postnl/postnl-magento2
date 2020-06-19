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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Shipment\Packingslip\Factory;
use Xtento\PdfCustomizer\Helper\Data;
use Xtento\PdfCustomizer\Model\Source\TemplateType;

class XtentoPdfCustomizer
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var DataHelperFactoryProxy
     */
    private $dataHelper;

    /**
     * @var GeneratePdfFactoryProxy
     */
    private $pdfGenerator;

    /**
     * XtentoPdfCustomiser constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param DataHelperFactoryProxy   $dataHelper
     * @param GeneratePdfFactoryProxy  $pdfGenerator
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        DataHelperFactoryProxy $dataHelper,
        GeneratePdfFactoryProxy $pdfGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->dataHelper = $dataHelper;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * @param Factory           $factory
     * @param ShipmentInterface $magentoShipment
     *
     * @return mixed
     */
    public function getPdf(ShipmentInterface $magentoShipment)
    {
        $orderId = $magentoShipment->getOrderId();
        $order = $this->orderRepository->get($orderId);

        $xtentoDataHelper = $this->dataHelper;
        $template = $xtentoDataHelper->getDefaultTemplate($order, TemplateType::TYPE_SHIPMENT);
        $templateId = $template->getId();

        $generatePdfHelper = $this->pdfGenerator;
        $document = $generatePdfHelper->generatePdfForObject('shipment', $magentoShipment->getId(), $templateId);

        return $document['output'];
    }

    /**
     * @return bool
     */
    public function isShipmentPdfEnabled()
    {
        $xtentoDataHelper = $this->dataHelper;

        if ($xtentoDataHelper->isEnabled(Data::ENABLE_SHIPMENT)) {
            return true;
        }

        return false;
    }
}
