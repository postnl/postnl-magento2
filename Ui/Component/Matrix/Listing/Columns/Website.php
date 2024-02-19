<?php

namespace TIG\PostNL\Ui\Component\Matrix\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Website extends Column
{
    /** @var WebsiteRepositoryInterface  */
    private $websiteRepository;

    /**
     * @param ContextInterface           $context
     * @param UiComponentFactory         $uiComponentFactory
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param array                      $components
     * @param array                      $data
     */
    public function __construct(
        ContextInterface           $context,
        UiComponentFactory         $uiComponentFactory,
        WebsiteRepositoryInterface $websiteRepository,
        array $components = [],
        array $data       = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->websiteRepository = $websiteRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$fieldName])) {
                    $websiteInformation = $this->websiteRepository->getById($item[$fieldName]);
                    $item[$fieldName]   = $websiteInformation->getName();
                }
            }
        }

        return $dataSource;
    }
}
