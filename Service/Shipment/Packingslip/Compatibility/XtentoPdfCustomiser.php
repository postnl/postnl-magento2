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

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Shipment\Packingslip\Factory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\ObjectManagerInterface;

class XtentoPdfCustomiser
{

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManagerInterface $objectManager
    ) {
        $this->orderRepository = $orderRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param Factory $factory
     * @param ShipmentInterface $magentoShipment
     *
     * @return string
     * @throws NotFoundException
     */
    public function getPdf(Factory $factory, ShipmentInterface $magentoShipment)
    {
        $orderId = $magentoShipment->getOrderId();

        $order = $this->orderRepository->get($orderId);
        $xtentoDataHelper = $this->objectManager->create('\Xtento\PdfCustomizer\Helper\Data');
        $templateId = $xtentoDataHelper->getDefaultTemplate(
            $order,
            \Xtento\PdfCustomizer\Model\Source\TemplateType::TYPE_SHIPMENT
        )->getId();

        $generatePdfHelper = $this->objectManager->create('\Xtento\PdfCustomizer\Helper\GeneratePdf');
        $document = $generatePdfHelper->generatePdfForObject('shipment', $magentoShipment->getId(), $templateId);

        return $document['output'];
    }

    /**
     * @return bool
     * @throws NotFoundException
     */
    public function isShipmentPdfEnabled()
    {
        $xtentoDataHelper = $this->objectManager->create('\Xtento\PdfCustomizer\Helper\Data');
        if ($xtentoDataHelper->isEnabled(\Xtento\PdfCustomizer\Helper\Data::ENABLE_SHIPMENT)) {
            return true;
        }

        return false;
    }

}
