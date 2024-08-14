<?php

namespace TIG\PostNL\Console\Config;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TIG\PostNL\Service\Export\ConfigExporter;

class Dump extends Command
{
    private const POSTNLCLI_COMMAND = 'postnl:config:dump';
    private const POSTNLCLI_COMMENT = 'Dump non-sensitive configurations';

    private const OPTION_ADD_RATE = 'add_matrix';

    /**
     * @var ConfigExporter
     */
    private $exporter;

    public function __construct
    (
        ConfigExporter $exporter,
        $name = null
    ) {
        parent::__construct($name);
        $this->exporter = $exporter;
    }

    protected function configure(): void
    {
        $this->setName(self::POSTNLCLI_COMMAND)
            ->setDescription(self::POSTNLCLI_COMMENT)
            ->setDefinition([
                new InputOption(
                    self::OPTION_ADD_RATE,
                    'm',
                    InputOption::VALUE_OPTIONAL,
                    'Add matrix rate to the export.',
                    0
                )
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('<info>Retrieving configurations.</info>');
            $configs = $this->exporter->retrieveConfigs();
            if ($input->getOption(self::OPTION_ADD_RATE) > 0) {
                $configs[ConfigExporter::MATRIX_RATE] = $this->exporter->dumpMatrixRate();
            }
            $output->writeln(\json_encode($configs, JSON_THROW_ON_ERROR));
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Synchronization completed</info>');

        return Cli::RETURN_SUCCESS;
    }
}
