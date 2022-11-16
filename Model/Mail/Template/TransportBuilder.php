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

namespace TIG\PostNL\Model\Mail\Template;

use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Magento\Framework\Mail\MessageInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @var array
     */
    private $parts = [];

    /**
     * @return $this|TransportBuilder
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();

        $mimeMessage = $this->getMimeMessage($this->message);

        if ($this->parts instanceof Part) {
            $mimeMessage->addPart($this->parts);
            $this->message->setBody($mimeMessage);
        }

        return $this;
    }

    /**
     * @param        $body
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param $filename
     *
     * @return $this
     */
    public function addAttachment(
        $body,
        string $mimeType    = Mime::TYPE_OCTETSTREAM,
        string $disposition = Mime::DISPOSITION_ATTACHMENT,
        string $encoding    = Mime::ENCODING_BASE64,
        $filename           = null
    ) {
        $this->parts = $this->createMimePart($body, $mimeType, $disposition, $encoding, $filename);
        return $this;
    }

    /**
     * @param $content
     * @param string $type
     * @param string $disposition
     * @param string $encoding
     * @param $filename
     *
     * @return Part
     */
    private function createMimePart(
        $content,
        string $type        = Mime::TYPE_OCTETSTREAM,
        string $disposition = Mime::DISPOSITION_ATTACHMENT,
        string $encoding    = Mime::ENCODING_BASE64,
        $filename           = null
    ) {
        $mimePart = new Part($content);
        $mimePart->setType($type);
        $mimePart->setDisposition($disposition);
        $mimePart->setEncoding($encoding);

        if ($filename) {
            $mimePart->setFileName($filename);
        }

        return $mimePart;
    }

    /**
     * @param MessageInterface $message
     *
     * @return MimeMessage
     */
    private function getMimeMessage(MessageInterface $message)
    {
        $body = $message->getBody();

        if ($body instanceof MimeMessage) {
            return $body;
        }

        $mimeMessage = new MimeMessage();

        if ($body) {
            $mimePart = $this->createMimePart((string)$body, Mime::TYPE_TEXT, Mime::DISPOSITION_INLINE);
            $mimeMessage->setParts([$mimePart]);
        }

        return $mimeMessage;
    }
}
