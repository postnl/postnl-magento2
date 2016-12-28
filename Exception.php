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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL;

class Exception extends \Exception
{
    public function __construct($message, $code = 0, $previous = null)
    {
        parent::__construct($message, 0, $previous);

        /**
         * Replace the code with the actual, non-integer code.
         */
        if ($code !== 0) {
            $code = (string) $code;
            $this->code = $code;
        }
    }

    /**
     * Custom __toString method that includes the error code, if present.
     *
     * @return string
     *
     * @see Exception::__toString()
     *
     * @link http://www.php.net/manual/en/exception.tostring.php
     */
    public function __toString()
    {
        $string = "exception '"
            . __CLASS__
            . "' with message '"
            . $this->getMessage()
            . "'";

        $code = $this->getCode();
        if ($code !== 0 && !empty($code)) {
            $string .= " and code '"
                . $this->getCode()
                . "'";
        }

        $string .= " in "
            . $this->getFile()
            . ':'
            . $this->getLine()
            . PHP_EOL
            . 'Stack trace:'
            . PHP_EOL
            . $this->getTraceAsString();

        return $string;
    }
}
