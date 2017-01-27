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

use TIG\PostNL\Model\ShipmentLabel;

/**
 * Class Positions
 *
 * @package TIG\PostNL\Helper\Pdf
 */
class Positions
{
    const LABEL_BORDER_MARGIN = 3.9;

    /**
     * @param int|float $width
     * @param int|float $height
     * @param string    $labelType
     * @param int|null  $position
     *
     * @return array
     */
    public function get($width, $height, $labelType = ShipmentLabel::BARCODE_TYPE_LABEL, $position = null)
    {
        $positionsResult = [];

        if ($labelType == ShipmentLabel::BARCODE_TYPE_LABEL || $labelType == ShipmentLabel::BARCODE_TYPE_RETURN) {
            $positionsResult = $this->getFourLabelsPerPage($width, $height);
        }

        if ($position != null) {
            $positionsResult = $positionsResult[$position];
        }

        return $positionsResult;
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
        $labelWidth = ($width / 2) - (self::LABEL_BORDER_MARGIN * 2);

        $position = [
            1 => ['x' => $posX,                     'y' => self::LABEL_BORDER_MARGIN, 'w' => $labelWidth],
            2 => ['x' => $posX,                     'y' => $posY,                     'w' => $labelWidth],
            3 => ['x' => self::LABEL_BORDER_MARGIN, 'y' => self::LABEL_BORDER_MARGIN, 'w' => $labelWidth],
            4 => ['x' => self::LABEL_BORDER_MARGIN, 'y' => $posY,                     'w' => $labelWidth]
        ];

        return $position;
    }
}
