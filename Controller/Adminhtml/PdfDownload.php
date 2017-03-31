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
use TIG\PostNL\Service\Shipment\Label\Generate;

class PdfDownload
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Generate
     */
    private $labelGenerator;

    /**
     * @param FileFactory $fileFactory
     * @param Generate    $labelGenerator
     */
    public function __construct(
        FileFactory $fileFactory,
        Generate $labelGenerator
    ) {
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
    }

    /**
     * @param $labels
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    // @codingStandardsIgnoreLine
    public function get($labels)
    {
        $pdfLabel = $this->labelGenerator->run($labels);

        return $this->fileFactory->create(
            'ShippingLabels.pdf',
            $pdfLabel,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
