<?php

namespace TIG\PostNL\Service\Export;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;

class ConfigExporter
{
    public const MATRIX_RATE = 'matrixrate';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var MatrixrateRepositoryInterface
     */
    private $matrixrateRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        MatrixrateRepositoryInterface $matrixrateRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->matrixrateRepository = $matrixrateRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function retrieveConfigs(): array
    {
        $data = $this->scopeConfig->getValue('tig_postnl');
        $data = $this->cleanConfigs($data);
        $carrierData = $this->scopeConfig->getValue('carriers/tig_postnl');
        unset(
            $carrierData['model'], // Constant
            $carrierData['title'], // Just a text
            $carrierData['specificerrmsg'],
            $carrierData['matrixrate_import'] // Garbage value saved
        );
        $data['carriers'] = $carrierData;
        return $data;
    }

    private function cleanConfigs(array $data): array
    {
        unset(
            // Remove unnecessary values
            $data['stability'],
            $data['tested_magento_version'],
            $data['developer_settings'], // Debug settings
            // Constant values
            $data['endpoints'],
            $data['track_and_trace']['service_url'],
            // Client api configs
            $data['generalconfiguration_extension_status'],
            // Customer address non-required values - leaving only address
            $data['generalconfiguration_shipping_address']['firstname'],
            $data['generalconfiguration_shipping_address']['lastname'],
            $data['generalconfiguration_shipping_address']['company'],
            $data['generalconfiguration_shipping_address']['department']
        );
        return $data;
    }

    public function dumpMatrixRate(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->matrixrateRepository->getList($searchCriteria);
        $result = [];
        foreach ($list->getItems() as $matrixRow) {
            $data = $matrixRow->getData();
            unset($data['entity_id'], $data['website_id']);
            $result[$matrixRow->getWebsiteId()][] = $data;
        }
        return $result;
    }

}
