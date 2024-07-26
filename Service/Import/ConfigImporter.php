<?php

namespace TIG\PostNL\Service\Import;

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Symfony\Component\Console\Output\OutputInterface;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate;
use TIG\PostNL\Service\Export\ConfigExporter;

class ConfigImporter
{
    /**
     * @var OutputInterface|null
     */
    private $output;

    /**
     * @var ConfigExporter
     */
    private $export;

    /**
     * @var ResourceConfig
     */
    private $resourceConfig;

    /**
     * @var Matrixrate
     */
    private $resource;

    public function __construct(
        ConfigExporter $export,
        ResourceConfig $resourceConfig
    ) {
        $this->export = $export;
        $this->resourceConfig = $resourceConfig;
    }

    public function setDebug(OutputInterface $output): self
    {
        $this->output = $output;
        return $this;
    }

    public function debug(string $message, int $verbosity = OutputInterface::VERBOSITY_VERBOSE): void
    {
        if ($this->output) {
            $this->output->writeln($message, $verbosity);
        }
    }

    public function readConfig(string $newConfig): ?array
    {
        if (!$newConfig || !isset($newConfig[0]) || $newConfig[0] !== '{') {
            return null;
        }
        try {
            return \json_decode($newConfig, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateConfigs(array $newValues): void
    {
        $currentValues = $this->export->retrieveConfigs();

        // Process updates to the carriers section
        if (isset($newValues['carriers'])) {
            $this->processUpdates('carriers/tig_postnl', $currentValues['carriers'], $newValues['carriers']);
        }
        unset($currentValues['carriers'], $newValues['carriers']);
        $matrix = $newValues[ConfigExporter::MATRIX_RATE] ?? null;
        unset($newValues[ConfigExporter::MATRIX_RATE]);
        // And all others
        $this->processUpdates('tig_postnl', $currentValues, $newValues);

        if ($matrix) {
            $this->debug('');
            $this->debug('Found matrix rates, processing data...');
            $this->processMatrixUpdates($matrix);
        }
    }

    private function processUpdates(string $path, array $oldConfigs, array $newConfig): void
    {
        foreach ($newConfig as $configKey => $newValue) {
            // For arrays - dive deeper in comparison
            $currentPath = $path . '/' . $configKey;
            if (is_array($newValue)) {
                $this->processUpdates($currentPath, $oldConfigs[$configKey] ?? [], $newValue);
                unset($newConfig[$configKey], $oldConfigs[$configKey]);
                continue;
            }

            // Compare values
            $oldValue = $oldConfigs[$configKey] ?? null;
            if ($oldValue === $newValue) {
                // Do not require any changes, skipping.
                unset($newConfig[$configKey], $oldConfigs[$configKey]);
                continue;
            }

            // Update config with new value
            $this->resourceConfig->saveConfig(
                $currentPath,
                $newValue,
                'default',
                0
            );
            $this->debug('Set <info>' . $currentPath . '</info> to ' . $newValue);
            unset($newConfig[$configKey], $oldConfigs[$configKey]);
        }

        // Now we need to remove all values, that wasn't processed in the newConfig
        foreach ($oldConfigs as $configKey => $oldValue) {
            $currentPath = $path . '/' . $configKey;
            if (is_array($oldValue)) {
                $this->processUpdates($currentPath, $oldValue, []);
                unset($newConfig[$configKey]);
                continue;
            }

            $this->debug('Removing <info>' . $currentPath . '</info>');
            $this->resourceConfig->deleteConfig($currentPath, 'default', 0);
        }
    }

    private function processMatrixUpdates(array $matrix): void
    {
        // Try to validate which websites we should update data to
        $connection = $this->resourceConfig->getConnection();
        // Load current websites
        $websiteSelect = $connection->select()
            ->from($connection->getTableName('store_website'));
        $websites = $connection->query($websiteSelect)->fetchAll();

        $processed = [];
        // Validate if we can assign website
        foreach ($matrix as $websiteId => $matrixData) {
            // Check if website id exists
            if (isset($websites[$websiteId])) {
                $this->prepareRows($processed, $websiteId, $matrixData);
                continue;
            }
            // Assume it's a website code
            foreach ($websites as $website) {
                if ($website['code'] === (string)$websiteId) {
                    $this->prepareRows($processed, $website['website_id'], $matrixData);
                    continue(2);
                }
            }
            $this->debug(
                '<error>Cannot find website '.$websiteId.' to associate matrix data.</error>. '.
                'Please update website it with the correct id or code.',
                OutputInterface::VERBOSITY_NORMAL
            );
            return;
        }

        $connection->beginTransaction();
        try {
            $table = $connection->getTableName('tig_postnl_matrixrate');
            $connection->delete($table);
            $connection->insertMultiple($table, $processed);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    private function prepareRows(array &$processed, int $websiteId, $matrixData): void
    {
        foreach ($matrixData as $row) {
            unset($row['entity_id'], $row['website_id']);
            $row['website_id'] = $websiteId;
            $processed[] = $row;
        }
    }

}
