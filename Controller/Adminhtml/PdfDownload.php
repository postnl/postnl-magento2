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
namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Message\ManagerInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Config\Source\Settings\LabelsizeSettings;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;
use TIG\PostNL\Service\Shipment\ShipmentService as Shipment;

class PdfDownload
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * @var LabelGenerate
     */
    private $labelGenerator;

    /**
     * @var PackingslipGenerate
     */
    private $packingslipGenerator;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var array
     */
    private $filteredLabels = [];

    const FILETYPE_PACKINGSLIP   = 'PackingSlips';
    const FILETYPE_SHIPPINGLABEL = 'ShippingLabels';

    /**
     * @param FileFactory            $fileFactory
     * @param Generate|LabelGenerate $labelGenerator
     * @param PackingslipGenerate    $packingslipGenerator
     */
    public function __construct(
        FileFactory $fileFactory,
        ManagerInterface $messageManager,
        Webshop $webshopConfig,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator,
        Shipment $shipment
    ) {
        $this->fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->webshopConfig = $webshopConfig;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
        $this->shipment = $shipment;
    }

    /**
     * @param $labels
     * @param $filename
     *
     * @return \Magento\Framework\App\ResponseInterface | \Magento\Framework\Message\ManagerInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    // @codingStandardsIgnoreLine
    public function get($labels, $filename = 'ShippingLabels')
    {
        if ($this->webshopConfig->getLabelSize() == LabelsizeSettings::A6_LABELSIZE) {
            $labels = $this->filterLabel($labels);
        }

        if (!$labels) {
            $this->messageManager->addErrorMessage(
                'No labels were created. If you\'re trying to generate Global Pack shipments, set your Label Size to A4. Please check your Label Size settings.'
            );
            return;
        }

        if (count($this->filteredLabels) >= 1) {
            $filteredShipments = $this->getShipmentIds($this->filteredLabels);
            $filteredShipments = implode(", ", $filteredShipments);

            $this->messageManager->addNoticeMessage(
                'Not all labels were created. Please check your Label Size settings. Labels are not generated for the following Shipment ID\'s: ' .
                $filteredShipments
            );
        }

        $pdfLabel = $this->generateLabel($labels, $filename);

        return $this->fileFactory->create(
            $filename . '.pdf',
            $pdfLabel,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }

    /**
     * @param $labels
     * @return array
     */
    private function filterLabel($labels) {
        return array_filter($labels, function($label) {
            /** @var \TIG\PostNL\Api\Data\ShipmentLabelInterface $label */
            $shouldRemove = false;

            if ($label->getType() !== 'gp') {
                $shouldRemove = true;
                $this->filteredLabels[] = $label->getParentId();
            }

            return $shouldRemove;
        });
    }

    /**
     * @param $labels
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShipmentIds($labels) {
        foreach ($labels as $label) {
            $shipmentIds[] = $this->shipment->getShipment($label)->getIncrementId();
        }

        return $shipmentIds;
    }

    /**
     * @param $labels
     * @param $filename
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    private function generateLabel($labels, $filename)
    {
        switch ($filename) {
            case static::FILETYPE_SHIPPINGLABEL:
                return $this->labelGenerator->run($labels);
            case static::FILETYPE_PACKINGSLIP:
                return $this->packingslipGenerator->run($labels);
            default:
                return $labels;
        }
    }
    // @codingStandardsIgnoreEnd
}
