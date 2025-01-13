<?php

namespace TIG\PostNL\Controller\Adminhtml\Config\Debug;

use Magento\Backend\App\Action\Context;
use Magento\Config\Controller\Adminhtml\System\AbstractConfig;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use TIG\PostNL\Service\Export\ConfigExporter;

class Dump extends AbstractConfig
{
    /**
     * @var ConfigExporter
     */
    private $exporter;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    public function __construct(
        Context              $context,
        Structure            $configStructure,
        ConfigSectionChecker $sectionChecker,
        FileFactory          $fileFactory,
        ConfigExporter       $exporter,
        ScopeConfigInterface $config
    ) {
        $this->fileFactory = $fileFactory;
        $this->exporter = $exporter;
        $this->config = $config;

        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(): ResponseInterface
    {
        try {
            $result = $this->exporter->retrieveConfigs();
            if ($this->config->isSetFlag('tig_postnl/developer_settings/config_dump/add_matrix')) {
                $result[ConfigExporter::MATRIX_RATE] = $this->exporter->dumpMatrixRate();
            }
            $result = \json_encode($result, JSON_THROW_ON_ERROR);
            return $this->fileFactory->create('postnl_config_' . date('Ymd') . '.json', $result);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Failed to collect configs: ' . $e->getMessage());
            return $this->_redirect('adminhtml/system_config/edit', ['section' => 'tig_postnl']);
        }
    }
}
