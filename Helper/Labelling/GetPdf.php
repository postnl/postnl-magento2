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
namespace TIG\PostNL\Helper\Labelling;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Shipping\Model\Shipping\LabelGenerator;

class GetPdf
{
    /**
     * @var LabelGenerator
     */
    private $labelGenerator;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @param LabelGenerator $labelGenerator
     * @param FileFactory    $fileFactory
     */
    public function __construct(
        LabelGenerator $labelGenerator,
        FileFactory $fileFactory
    ) {
        $this->labelGenerator = $labelGenerator;
        $this->fileFactory = $fileFactory;
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
        /** @var \Zend_Pdf $combinedLabels */
        $combinedLabels = $this->labelGenerator->combineLabelsPdf($labels);
        $renderedLabels = $combinedLabels->render();

        return $this->fileFactory->create(
            'ShippingLabels.pdf',
            $renderedLabels,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
