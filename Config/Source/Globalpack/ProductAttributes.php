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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Source\Globalpack;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class ProductAttributes implements OptionSourceInterface
{
    private $attributesRepository;

    private $searchCriteriaBuilder;

    /**
     * ProductAttributes constructor.
     *
     * @param AttributeRepositoryInterface $attributeRepository
     * @param SearchCriteriaBuilder        $criteriaBuilder
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->attributesRepository  = $attributeRepository;
        $this->searchCriteriaBuilder = $criteriaBuilder;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->attributesRepository->getList(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $this->searchCriteriaBuilder->create()
        );

        $options = [];
        foreach ($attributes->getItems() as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getFrontendLabel()
            ];
        }

        return $options;
    }
}
