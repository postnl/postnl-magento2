<?php

namespace TIG\PostNL\Service\Framework;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

// @codingStandardsIgnoreFile
class FileFactory
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var bool
     */
    private $isFile = false;

    /**
     * @var null|string
     */
    private $file = null;

    /**
     * FileFactory constructor.
     *
     * @param ResponseInterface $response
     * @param Filesystem        $filesystem
     */
    public function __construct(
        ResponseInterface $response,
        Filesystem $filesystem
    ) {
        $this->response   = $response;
        $this->filesystem = $filesystem;
    }

    public function create(
        $fileName,
        $content,
        $responseType = 'inline',
        $baseDir = DirectoryList::VAR_DIR,
        $contentType = 'application/pdf',
        $contentLength = null
    ) {
        $dir = $this->filesystem->getDirectoryWrite($baseDir);
        if (is_array($content)) {
            $contentLength = $this->getContentLenghtAndSetFile($dir, $content);
        }

        $this->response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', $contentLength === null ? strlen((string)$content) : $contentLength, true)
            ->setHeader('Content-Disposition', $responseType.'; filename="' . $fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true);

        if ($content !== null) {
            $this->openStreamAndFlush($fileName, $content, $dir);
        }

        return $this->response;
    }

    /**
     * @param $fileName
     * @param $content
     * @param WriteInterface $dir
     */
    private function openStreamAndFlush($fileName, $content, $dir)
    {
        $this->response->sendHeaders();
        if ($this->isFile) {
            $stream = $dir->openFile($this->file, 'r');
            while (!$stream->eof()) {
                echo $stream->read(1024);
            }
        } else {
            $dir->writeFile($fileName, $content);
            $stream = $dir->openFile($fileName, 'r');
            while (!$stream->eof()) {
                echo $stream->read(1024);
            }
        }

        $stream->close();
        flush();
        if (!empty($content['rm'])) {
            $dir->delete($this->file);
        }
    }

    /**
     * @param WriteInterface $dir
     * @param $content
     *
     * @return string
     * @throws \Exception
     */
    private function getContentLenghtAndSetFile($dir, $content)
    {
        if (!isset($content['type']) || !isset($content['value'])) {
            throw new \InvalidArgumentException("Invalid arguments. Keys 'type' and 'value' are required.");
        }

        if ($content['type'] !== 'filename') {
            return null;
        }

        $this->isFile = true;
        $this->file   = $content['value'];
        if (!$dir->isFile($this->file)) {
            throw new \Exception(__('File not found'));
        }

        return $dir->stat($this->file)['size'];
    }
}