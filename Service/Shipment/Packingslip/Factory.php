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

use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Factory
 *
 * This is needed so we can check if Fooman is installed or not.
 * We can not use DI, because compilation will fail if Fooman is not installed. That why we use the objectManager.
 *
 * @package TIG\PostNL\Service\Shipment\Packingslip
 */
class Factory
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PdfShipment
     */
    private $magentoPdf;

    /**
     * @var int
     */
    private $yCoordinate = 0;

    /**
     * Factory constructor.
     *
     * @param Manager                  $manager
     * @param ObjectManagerInterface   $objectManager
     * @param PdfShipment              $pdfShipment
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Manager $manager,
        ObjectManagerInterface $objectManager,
        PdfShipment $pdfShipment,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->moduleManager   = $manager;
        $this->objectManager   = $objectManager;
        $this->magentoPdf      = $pdfShipment;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param ShipmentInterface $magentoShipment
     * @param bool $forceMagento
     *
     * @return string
     */
    public function create($magentoShipment, $forceMagento = false)
    {
        if (!$this->moduleManager->isEnabled('Fooman_PrintOrderPdf') || $forceMagento) {
            $renderer = $this->magentoPdf->getPdf([$magentoShipment]);
            // @codingStandardsIgnoreLine
            $this->setY($this->magentoPdf->y);
            return $renderer->render();
        }

        return $this->getFoomanPdf($magentoShipment->getOrderId());
    }

    /**
     * @param $orderId
     *
     * @return string
     * @throws NotFoundException
     */
    private function getFoomanPdf($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        /** @var \Fooman\PdfCustomiser\Block\OrderShipmentFactory $documentManager */
        // @codingStandardsIgnoreLine
        $documentManager = $this->objectManager->create('\Fooman\PdfCustomiser\Block\OrderShipmentFactory');
        $document = $documentManager->create(['data' => ['order' => $order]]);

        /** @var \Fooman\PdfCore\Model\PdfRenderer $foomanRenderer */
        // @codingStandardsIgnoreLine
        $foomanRenderer = $this->objectManager->create('\Fooman\PdfCore\Model\PdfRenderer');
        $foomanRenderer->addDocument($document);

        if (!$foomanRenderer->hasPrintContent()) {
            throw new NotFoundException(__('Nothing to print'));
        }

        $this->setY(500);

        return $foomanRenderer->getPdfAsString();
    }

    /**
     * @return int
     */
    public function getY()
    {
        return $this->yCoordinate;
    }

    /**
     * @param $cordinate
     */
    public function setY($cordinate)
    {
        $this->yCoordinate = $cordinate;
    }
}
