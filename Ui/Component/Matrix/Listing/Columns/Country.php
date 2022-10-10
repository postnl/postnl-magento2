<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
