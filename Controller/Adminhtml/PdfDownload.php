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
use TIG\PostNL\Service\Shipment\Label\Generate as LabelGenerate;
use TIG\PostNL\Service\Shipment\Packingslip\Generate as PackingslipGenerate;

class PdfDownload
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var LabelGenerate
     */
    private $labelGenerator;

    /**
     * @var PackingslipGenerate
     */
    private $packingslipGenerator;

    const FILETYPE_PACKINGSLIP   = 'PackingSlips';
    const FILETYPE_SHIPPINGLABEL = 'ShippingLabels';

    /**
     * @param FileFactory            $fileFactory
     * @param Generate|LabelGenerate $labelGenerator
     * @param PackingslipGenerate    $packingslipGenerator
     */
    public function __construct(
        FileFactory $fileFactory,
        LabelGenerate $labelGenerator,
        PackingslipGenerate $packingslipGenerator
    ) {
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        $this->packingslipGenerator = $packingslipGenerator;
    }

    /**
     * @param $labels
     * @param $filename
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    // @codingStandardsIgnoreLine
    public function get($labels, $filename = 'ShippingLabels')
    {
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
