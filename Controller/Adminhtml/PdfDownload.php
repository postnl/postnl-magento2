<?php

namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Config\Provider\PrintSettingsConfiguration;
use TIG\PostNL\Config\Source\Settings\LabelsizeSettings;
use TIG\PostNL\Config\Source\Settings\LabelTypeSettings;
use TIG\PostNL\Service\Framework\FileFactory;
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;
use TIG\PostNL\Service\Shipment\ShipmentService as Shipment;

// @codingStandardsIgnoreFile
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
     * @var PrintSettingsConfiguration
     */
    private $printSettings;

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
    private FileDownload $fileDownload;

    /**
     * @var array
     */
    private $filteredLabels = [];

    const FILETYPE_PACKINGSLIP   = 'PackingSlips';
    const FILETYPE_SHIPPINGLABEL = 'ShippingLabels';

    /**
     * PdfDownload constructor.
     *
     * @param FileFactory $fileFactory
     * @param ManagerInterface $messageManager
     * @param PrintSettingsConfiguration $printSettings
     * @param LabelGenerate $labelGenerator
     * @param PackingslipGenerate $packingslipGenerator
     * @param Shipment $shipment
     * @param FileDownload $fileDownload
     */
    public function __construct(
        FileFactory $fileFactory,
        ManagerInterface $messageManager,
        PrintSettingsConfiguration $printSettings,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator,
        Shipment $shipment,
        FileDownload $fileDownload
    ) {
        $this->fileFactory = $fileFactory;
        $this->messageManager = $messageManager;
        $this->printSettings = $printSettings;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
        $this->shipment = $shipment;
        $this->fileDownload = $fileDownload;
    }

    /**
     * @param ShipmentLabelInterface[]|string[] $labels
     * @param string $filename
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Message\ManagerInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    // @codingStandardsIgnoreLine
    public function get($labels, $filename = 'ShippingLabels')
    {
        if ($this->printSettings->getLabelSize() == LabelsizeSettings::A6_LABELSIZE
            && $filename !== 'PackingSlips'
        ) {
            $labels = $this->filterLabel($labels);
        }

        if (!$labels) {
            $this->setEmptyLabelsResponse();
            // @codingStandardsIgnoreLine
            /** @todo : find a better solution to close the new browser tab. */
            echo "<script>window.close();</script>";
            return;
        }

        if (count($this->filteredLabels) > 0) {
            $this->setSkippedLabelsResponse();
        }

        if ($this->isAllPdfLabels($labels)) {
            $pdfLabel = $this->generateLabel($labels, $filename);

            return $this->fileFactory->create(
                $filename . '.pdf',
                $pdfLabel,
                $this->printSettings->getLabelResponse()
            );
        }
        return $this->fileDownload->returnFiles($labels, $filename);
    }

    /**
     * @param $labels
     * @return array
     */
    private function filterLabel($labels)
    {
        return array_filter($labels, function ($label) {
            if (is_array($label)) {
                return false;
            }

            return true;
        });
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    private function getShipmentIds()
    {
        $shipmentIds = [];
        foreach ($this->filteredLabels as $identifier) {
            $postNLShipment = $this->shipment->getPostNLShipment($identifier);
            $shipment       = $postNLShipment->getShipment();
            $shipmentIds[]  = $shipment->getIncrementId();
        }

        return implode(", ", $shipmentIds);
    }

    /**
     * Set empty labels response.
     */
    private function setEmptyLabelsResponse()
    {
        $this->messageManager->addWarningMessage(
        // @codingStandardsIgnoreLine
            __('No labels were created.')
        );
    }

    /**
     * Set response message for shipment where the label is not printed.
     */
    private function setSkippedLabelsResponse()
    {
        $this->messageManager->addNoticeMessage(
        // @codingStandardsIgnoreLine
            __(
                'Not all labels were created. Please check your Label Size settings. Labels are not generated for the following Shipment(s): %1',
                $this->getShipmentIds()
            )
        );
    }

    /**
     * @param $labels
     * @param $filename
     *
     * @return string
     * @throws \TIG\PostNL\Exception
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

    /**
     * @param ShipmentLabelInterface[]|string[] $labels
     * @return bool
     */
    private function isAllPdfLabels(array $labels): bool
    {
        $result = true;
        foreach ($labels as $label) {
            if (is_string($label)) {
                // Package Slips - legacy - instead of object it pass string and this sting is always PDF.
                continue;
            }
            if ($label->getLabelFileFormat() !== LabelTypeSettings::TYPE_PDF) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    public function emptyResponse()
    {
        return $this->fileDownload->emptyResponse();
    }
}
