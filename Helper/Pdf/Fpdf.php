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

use TIG\PostNL\Config\Provider\Webshop;

/**
 * Class Fpdf
 *
 * @package TIG\PostNL\Helper\Pdf
 */
class Fpdf extends \FPDI
{
    const MAX_LABELS_PER_PAGE = 4;

    const PAGE_SIZE_A6 = array(105, 148);

    /** @var int $labelCounter */
    private $labelCounter;

    /**
     * @var Positions
     */
    private $positions;

    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @param Positions $positions
     * @param Webshop   $webshop
     * @param string    $orientation
     * @param string    $unit
     * @param string    $size
     */
    public function __construct(
        Positions $positions,
        Webshop $webshop,
        $orientation = 'P',
        $unit = 'mm',
        $size = 'A4'
    ) {
        $this->positions = $positions;
        $this->webshop = $webshop;
        $this->setLabelCounter(self::MAX_LABELS_PER_PAGE);

        parent::__construct($orientation, $unit, $size);
    }

    /**
     * @param string $labelFileName
     * @param string $labelType
     */
    public function addLabel($labelFileName, $labelType)
    {
        $this->updatePage();

        $pdfPageWidth = $this->GetPageWidth();
        $pdfPageHeight = $this->GetPageHeight();
        $position = $this->positions->get($pdfPageWidth, $pdfPageHeight, $labelType, $this->getLabelCounter());

        $this->setSourceFile($labelFileName);
        $templateIndex = $this->importPage(1);
        $this->useTemplate($templateIndex, $position['x'], $position['y'], $position['w']);
    }

    /**
     * @param int $labelCount
     *
     * @return int
     */
    public function updatePage()
    {
        $this->increaseLabelCounter();

        $labelSize = $this->webshop->getLabelSize();

        if ($labelSize == 'A6') {
            $this->setLabelCounter(3);
            $this->AddPage('L', self::PAGE_SIZE_A6);
        }

        if ($this->getLabelCounter() > self::MAX_LABELS_PER_PAGE) {
            $this->resetLabelCounter();
            $this->AddPage('L', 'A4');
        }
    }

    /**
     * @return int
     */
    public function getLabelCounter()
    {
        return $this->labelCounter;
    }

    /**
     * @param int $labelCounter
     *
     * @return $this
     */
    public function setLabelCounter($labelCounter)
    {
        $this->labelCounter = $labelCounter;
    }

    public function increaseLabelCounter()
    {
        $this->labelCounter++;
    }

    public function resetLabelCounter()
    {
        $this->labelCounter = 1;
    }
}
