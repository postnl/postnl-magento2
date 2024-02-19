<?php

namespace TIG\PostNL\Ui\Component\Matrix\Listing\Columns;

use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Country extends Column
{
    /** @var CountryInformationAcquirerInterface  */
    private $countryInformationAcquirer;

    /**
     * @param ContextInterface                    $context
     * @param UiComponentFactory                  $uiComponentFactory
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param array                               $components
     * @param array                               $data
     */
    public function __construct(
        ContextInterface                    $context,
        UiComponentFactory                  $uiComponentFactory,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        array $components = [],
        array $data       = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->countryInformationAcquirer = $countryInformationAcquirer;
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
                $itemList        = explode(',',$item[$fieldName]);
                $countryNameList = [];

                foreach ($itemList as $value) {
                    $countryInfo       = $this->countryInformationAcquirer->getCountryInfo($value);
                    $countryNameList[] = $countryInfo->getFullNameLocale();
                }

                $item[$fieldName] = implode(', ',$countryNameList);
            }
        }

        return $dataSource;
    }
}
