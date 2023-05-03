<?php

namespace TIG\PostNL\Service\Import;

use Magento\Framework\Phrase;

class ParseErrors
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return count($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $message
     */
    public function addError($message)
    {
        if ($message instanceof Phrase) {
            $message = $message->render();
        }

        $this->errors[] = $message;
    }

    /**
     * Reset the errors array
     */
    public function resetErrors()
    {
        $this->errors = [];
    }

    /**
     * @param $errors
     */
    public function addErrors($errors)
    {
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }
}
