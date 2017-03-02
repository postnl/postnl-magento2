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
namespace TIG\PostNL\Helper\Pdf;

use TIG\PostNL\Model\ShipmentLabel;

class Positions
{
    const LABEL_BORDER_MARGIN = 3.9;

    /**
     * @param int|float $width
     * @param int|float $height
     * @param string    $labelType
     *
     * @return array
     */
    public function get($width, $height, $labelType = ShipmentLabel::BARCODE_TYPE_LABEL)
    {
        $positionsResult = [];

        if ($labelType == ShipmentLabel::BARCODE_TYPE_LABEL || $labelType == ShipmentLabel::BARCODE_TYPE_RETURN) {
            $positionsResult = $this->getFourLabelsPerPage($width, $height);
        }

        return $positionsResult;
    }

    /**
     * @param int|float $width
     * @param int|float $height
     * @param int       $position
     * @param string    $labelType
     *
     * @return array
     */
    public function getForPosition($width, $height, $position, $labelType = ShipmentLabel::BARCODE_TYPE_LABEL)
    {
        $foundPosition = $this->get($width, $height, $labelType);

        if (!empty($foundPosition) && isset($foundPosition[1])) {
            $foundPosition = $foundPosition[$position];
        }

        return $foundPosition;
    }

    /**
     * @param int|float $width
     * @param int|float $height
     *
     * @return array
     */
    public function getFourLabelsPerPage($width, $height)
    {
        $posX = ($width / 2) + self::LABEL_BORDER_MARGIN;
        $posY = ($height / 2) + self::LABEL_BORDER_MARGIN;
        $labelWidth = 141.6;

        $position = [
            1 => ['x' => $posX,                     'y' => self::LABEL_BORDER_MARGIN, 'w' => $labelWidth],
            2 => ['x' => $posX,                     'y' => $posY,                     'w' => $labelWidth],
            3 => ['x' => self::LABEL_BORDER_MARGIN, 'y' => self::LABEL_BORDER_MARGIN, 'w' => $labelWidth],
            4 => ['x' => self::LABEL_BORDER_MARGIN, 'y' => $posY,                     'w' => $labelWidth]
        ];

        return $position;
    }
}
