<?php

namespace TIG\PostNL\Controller\Adminhtml;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RawFactory;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Config\Provider\PrintSettingsConfiguration;
use TIG\PostNL\Service\Framework\FileFactory;

class FileDownload
{
    private FileFactory $fileFactory;
    private RawFactory $rawFactory;
    private PrintSettingsConfiguration $printSettings;
    private UrlInterface $urlBuilder;

    public function __construct(
        FileFactory $fileFactory,
        PrintSettingsConfiguration $printSettings,
        RawFactory $rawFactory,
        UrlInterface $urlBuilder
    ) {
        $this->rawFactory = $rawFactory;
        $this->fileFactory = $fileFactory;
        $this->printSettings = $printSettings;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param ShipmentLabelInterface[] $labels
     * @param string $filename
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Message\ManagerInterface
     */
    public function returnFiles(array $labels, string $filename)
    {
        if (\count($labels) === 1) {
            $label = array_pop($labels);
            return $this->returnFile($label, $filename);
        }

        return $this->returnInFrames($labels, $filename);
    }

    public function emptyResponse()
    {
        $result = $this->rawFactory->create();
        $result->setContents('File not found');
        return $result;
    }

    public function returnFile(ShipmentLabelInterface $label, string $filename): ResponseInterface
    {
        $extension = strtolower($label->getLabelFileFormat());
        $contentType = [
            'jpg' => 'application/jpeg',
            'gif' => 'application/gif',
            'zpl' => 'application/text'
        ];
        $application = $contentType[$extension] ?? 'application/text';
        return $this->fileFactory->create(
            $filename . '_' . $label->getEntityId() . '.' . $extension,
            base64_decode($label->getLabel()),
            $this->printSettings->getLabelResponse(),
            DirectoryList::VAR_DIR,
            $application
        );
    }

    private function returnInFrames(array $labels, string $filename)
    {
        $result = $this->rawFactory->create();
        $content = '<html><body>
                '. $this->generateLinks($labels, $filename) . '
                <button type="button" onclick="window.close();">Close</button>
            <script>
                let timeout = 2000;
                const links = document.getElementsByClassName(\'clickme\');
                for(let i=0;i<links.length;i++) {
                    setTimeoutWo(links[i], timeout);
                    timeout += 2000;
                }
                function setTimeoutWo(target, timeout) {
                    window.setTimeout(function () {
                        target.click();
                    }, timeout);
                }
            </script>
            </body>';
        $result->setContents($content);

        return $result;
    }

    /**
     * @param ShipmentLabelInterface[] $labels
     * @param string $filename
     *
     * @return string
     */
    private function generateLinks(array $labels, string $filename): string
    {
        $content = '';
        $i = 0;
        foreach ($labels as $label) {
            $url = $this->urlBuilder->getUrl('postnl/shipment/downloadLabel', [
                'id' => $label->getEntityId(),
                'name' => $filename
            ]);
            $labelFilename = $this->getFileName($label, $filename);
            $class = '';
            if ($i === 0) {
                $content .= '<iframe src="'.$url.'" width="10" height="10" style="visibility: hidden;"></iframe>';
            } else {
                $class = 'clickme';
            }
            $content .= '<p><a
                id="label_'.$label->getEntityId().'" class="link '.$class.'"
                title="Label file" download="'.$labelFilename.'" target="_blank"
                href="'.$url.'">'.$labelFilename.'</a></p>';
            $i++;
        }
        return $content;
    }

    private function getFileName(ShipmentLabelInterface $label, string $filename): string
    {
        $extension = strtolower($label->getLabelFileFormat());
        $filename = $filename . '_';
        if ($label->getReturnLabel() > 0) {
            $filename .= 'Return_';
        }
        $filename .= $label->getEntityId() . '.' . $extension;
        return $filename;
    }
}
