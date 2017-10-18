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
namespace TIG\PostNL;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * We are getting code sniffer warnings that we cannot execute code in the constructor. This makes sense for objects
 * that are used in dependency injection. As this is a Exception, this is not the case. So that's why we are disabling
 * the code sniffer for this file.
 *
 * @package TIG\PostNL
 */
class Exception extends LocalizedException
{
    private $exceptionMessage;

    /**
     * @param Phrase|string $message
     * @param int           $code
     * @param null          $previous
     */
    // @codingStandardsIgnoreStart
    public function __construct($message, $code = 0, $previous = null)
    {
        $message = $this->getMessageString($message);

        // @codingStandardsIgnoreLine
        $this->exceptionMessage = __($message);

        if ($code !== 0) {
            $code = (string) $code;
            $this->code = $code;

            $message = '[' . $code . '] ' . $message;
        }

        if (is_string($message)) {
            // @codingStandardsIgnoreLine
            $message = __($message);
        }

        parent::__construct($message, $previous);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param $message
     *
     * @return string
     */
    private function getMessageString($message)
    {
        if ($message instanceof Phrase) {
            return $message->render();
        }

        return $message;
    }
}
