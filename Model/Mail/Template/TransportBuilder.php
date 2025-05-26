<?php

namespace TIG\PostNL\Model\Mail\Template;

use Magento\Framework\Mail\MessageInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    private array $parts = [];

    /**
     * @return $this|TransportBuilder
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();

        if (!empty($this->parts)) {
            $this->attachFile($this->message);
        }

        return $this;
    }

    /**
     * @param        $body
     * @param string $mimeType
     * @param $filename
     *
     * @return $this
     */
    public function addAttachment(
        $body,
        string $mimeType    = 'application/octet-stream',
        $filename           = null
    ) {
        $this->parts[] = [
            'body' => $body,
            'mime' => $mimeType,
            'name' => $filename
        ];
        return $this;
    }

    /**
     * @param $content
     * @param string $type
     * @param string $disposition
     * @param string $encoding
     * @param $filename
     *
     * @return \Laminas\Mime\Part
     */
    private function createMimePart(
        $content,
        string $type        = \Laminas\Mime\Mime::TYPE_OCTETSTREAM,
        string $disposition = \Laminas\Mime\Mime::DISPOSITION_ATTACHMENT,
        string $encoding    = \Laminas\Mime\Mime::ENCODING_BASE64,
        $filename           = null
    ) {
        $mimePart = new \Laminas\Mime\Part($content);
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
     */
    private function attachFile(MessageInterface $message): void
    {
        // Magento 2.4.8+ support - using Symfony classes, laminas removed as deprecated
        if (method_exists($message, 'getSymfonyMessage')) {
            /** @var \Symfony\Component\Mime\Message $symfonyMessage */
            $symfonyMessage = $message->getSymfonyMessage();
            $part = $symfonyMessage->getBody();
            $attachments = [];
            foreach ($this->parts as $partData) {
                $attachments[] = new \Symfony\Component\Mime\Part\DataPart($partData['body'], $partData['name'], $partData['mime']);
            }
            $part = new \Symfony\Component\Mime\Part\Multipart\MixedPart($part, ...$attachments);
            $symfonyMessage->setBody($part);
            return;
        }

        // Magento 2.4.8- support - laminas messages
        if (class_exists('Laminas\Mime\Message')) {
            $mimeMessage = $message->getBody();
            if (!$mimeMessage instanceof \Laminas\Mime\Message) {
                $body = $mimeMessage;
                $mimeMessage = new \Laminas\Mime\Message();

                if ($body) {
                    $mimePart = $this->createMimePart(
                        (string)$body,
                        \Laminas\Mime\Mime::TYPE_TEXT,
                        \Laminas\Mime\Mime::DISPOSITION_INLINE
                    );
                    $mimeMessage->setParts([$mimePart]);
                }
            }
            foreach ($this->parts as $partData) {
                $part = $this->createMimePart($partData['body'], $partData['mime'], 'attachment', 'base64', $partData['name']);
                $mimeMessage->addPart($part);
            }
            $this->message->setBody($mimeMessage);
        }

    }
}
