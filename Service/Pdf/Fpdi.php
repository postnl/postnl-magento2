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
namespace TIG\PostNL\Service\Pdf;

// @codingStandardsIgnoreFile
/**
 * Original:
 * http://www.fpdf.org/en/script/script2.php
 */
class Fpdi extends \FPDI
{
    const PAGE_SIZE_A6 = [105, 148];
    const PAGE_SIZE_A6_WIDTH = 105;
    const PAGE_SIZE_A6_HEIGHT = 148;

    private $angle = 0;

    /**
     * @param     $angle
     * @param int $x
     * @param int $y
     */
    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) {
            $x = $this->x;
        }

        if ($y == -1) {
            $y = $this->y;
        }

        if ($this->angle != 0) {
            $this->_out('Q');
        }

        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(
                sprintf(
                    'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy
                )
            );
        }
    }

    /**
     * Converts pixels to points. 3.8 pixels is 1 pt in pdfs.
     *
     * @param int $pixels
     *
     * @return float|int
     */
    function pixelsToPoints($pixels = 0)
    {
        $points = 0;

        if ($pixels != 0) {
            $points = round($pixels / 3.8, 1);
        }

        return $points;
    }

    /**
     * @param string   $file
     * @param null|int $xPosition
     * @param null|int $yPosition
     * @param int      $width
     */
    function addSinglePage($file, $xPosition = null, $yPosition = null, $width = 0)
    {
        $this->setSourceFile($file);
        $pageId = $this->importPage(1);
        $this->useTemplate($pageId, $xPosition, $yPosition, $width);
    }

    /**
     * @param string   $file
     * @param null|int $xPosition
     * @param null|int $yPosition
     * @param int      $width
     */
    function addMultiplePages($file, $xPosition = null, $yPosition = null, $width = 0)
    {
        $pages = $this->setSourceFile($file);

        for ($page = 1; $page <= $pages; $page++) {
            $this->AddPage('P', 'A4');
            $pageId = $this->importPage($page);
            $this->useTemplate($pageId, $xPosition, $yPosition, $width);
        }
    }

    /**
     * Close the page.
     */
    protected function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }

        parent::_endpage();
    }
}
