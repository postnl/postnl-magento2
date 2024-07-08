<?php

namespace TIG\PostNL\Console\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TIG\PostNL\Service\Export\ConfigExporter;
use TIG\PostNL\Service\Import\ConfigImporter;


class Import extends Command
{
    private const POSTNLCLI_COMMAND = 'postnl:config:import';
    private const POSTNLCLI_COMMENT = 'Uploads and rewrites configurations from a previous dump.';

    /**
     * @var ConfigExporter
     */
    private $exporter;

    /**
     * @var ConfigImporter
     */
    private $importer;

    /**
     * @var DirectoryList
     */
    private $dir;

    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    public function __construct
    (
        ConfigExporter $exporter,
        ConfigImporter $importer,
        DirectoryList $dir,
        TypeListInterface $cacheTypeList,
         $name = null
    ) {
        parent::__construct($name);
        $this->exporter = $exporter;
        $this->importer = $importer;
        $this->dir = $dir;
        $this->cacheTypeList = $cacheTypeList;
    }

    protected function configure(): void
    {
        $this->setName(self::POSTNLCLI_COMMAND)
            ->setDescription(self::POSTNLCLI_COMMENT)
            ->setDefinition([
            new InputArgument(
                'file',
                InputArgument::REQUIRED,
                'Config file path/name in the magento var/ folder.'
            ),
        ]);

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        try {
            $file = $this->dir->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . $filePath;
            if (!file_exists($file)) {
                $output->writeln('<error>' . 'Cannot find or read provided file:  ' . $file . '</error>');
                return Cli::RETURN_FAILURE;
            }

            $newConfig = \file_get_contents($file);

            $output->writeln('<info>Read config:</info>', OutputInterface::VERBOSITY_DEBUG);
            $output->writeln($newConfig, OutputInterface::VERBOSITY_DEBUG);

            $newConfig = $this->importer->readConfig($newConfig);
            if ($newConfig === null) {
                $output->writeln('<error>' . 'Incorrect config value provided' . '</error>');
                return Cli::RETURN_FAILURE;
            }
            $output->writeln('<info>Input value:</info>', OutputInterface::VERBOSITY_DEBUG);
            $output->writeln(print_r($newConfig, true), OutputInterface::VERBOSITY_DEBUG);

            $output->writeln('');
            $output->writeln('<info>Just in case here is current configurations:</info>');
            $oldConfigs = $this->exporter->retrieveConfigs();
            if (isset($newConfig[ConfigExporter::MATRIX_RATE])) {
                $oldConfigs[ConfigExporter::MATRIX_RATE] = $this->exporter->dumpMatrixRate();
            }
            $output->writeln(\json_encode($oldConfigs, JSON_THROW_ON_ERROR));

            $output->writeln('');
            $this->importer->setDebug($output)->updateConfigs($newConfig);

            $output->writeln('<info>Update completed.</info>');
            $this->cacheTypeList->cleanType('config');
            $output->writeln('<info>Config cache cleared.</info>');
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }


        return Cli::RETURN_SUCCESS;
    }

}
