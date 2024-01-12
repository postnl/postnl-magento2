<?php

namespace TIG\PostNL;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * We are getting code sniffer warnings that we cannot execute code in the constructor. This makes sense for objects
 * that are used in dependency injection. As this is a Exception, this is not the case. So that's why we are disabling
 * the code sniffer for this file.
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
