<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;

/**
 * Class Get
 *
 * @package TIG\PostNL\Helper\Pdf
 */
class Get
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Generate
     */
    private $generatePdf;

    /**
     * @param FileFactory    $fileFactory
     * @param Generate       $generatePdf
     */
    public function __construct(
        FileFactory $fileFactory,
        Generate $generatePdf
    ) {
        $this->fileFactory = $fileFactory;
        $this->generatePdf = $generatePdf;
    }

    /**
     * @param $labels
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Exception
     * @throws \Zend_Pdf_Exception
     */
    public function get($labels)
    {
        $pdfLabel = $this->generatePdf->get($labels);

        return $this->fileFactory->create(
            'ShippingLabels.pdf',
            $pdfLabel,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
