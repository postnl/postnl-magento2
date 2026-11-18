<?php

namespace TIG\PostNL\Model\Mail\Template;

use Magento\Framework\Mail\MessageInterface;
use function class_exists;

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
     * @param \Laminas\Mime\Part $mimePart
     * @param string $type
     * @param string $disposition
     * @param string $encoding
     * @param $filename
     *
     * @return \Laminas\Mime\Part
     */
    private function configureMimePart(
        $mimePart,
        string $type        = 'application/octet-stream',
        string $disposition = 'attachment',
        string $encoding    = 'base64',
        $filename           = null
    ) {
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
        if (method_exists($message, 'getSymfonyMessage')) {
            $this->attachSymfonyFiles($message->getSymfonyMessage());
            return;
        }

        if (class_exists('Laminas\Mime\Message') && class_exists('Laminas\Mime\Part')) {
            $this->attachLaminasFiles($message, 'Laminas\Mime\Message', 'Laminas\Mime\Part');
        }
    }

    private function attachSymfonyFiles(\Symfony\Component\Mime\Message $symfonyMessage): void
    {
        $part = $symfonyMessage->getBody();
        $attachments = [];
        foreach ($this->parts as $partData) {
            $attachments[] = new \Symfony\Component\Mime\Part\DataPart($partData['body'], $partData['name'], $partData['mime']);
        }
        $part = new \Symfony\Component\Mime\Part\Multipart\MixedPart($part, ...$attachments);
        $symfonyMessage->setBody($part);
    }

    private function attachLaminasFiles(MessageInterface $message, string $messageClass, string $partClass): void
    {
        $mimeMessage = $this->getLaminasBody($message, $messageClass, $partClass);
        foreach ($this->parts as $partData) {
            $part = $this->configureMimePart(
                new $partClass($partData['body']),
                $partData['mime'],
                'attachment',
                'base64',
                $partData['name']
            );
            $mimeMessage->addPart($part);
        }
        $this->message->setBody($mimeMessage);
    }

    private function getLaminasBody(MessageInterface $message, string $messageClass, string $partClass)
    {
        $body = $message->getBody();
        if ($body instanceof $messageClass) {
            return $body;
        }

        $mimeMessage = new $messageClass();
        if ($body) {
            $mimePart = $this->configureMimePart(new $partClass((string) $body), 'text/plain', 'inline');
            $mimeMessage->setParts([$mimePart]);
        }
        return $mimeMessage;
    }
}
