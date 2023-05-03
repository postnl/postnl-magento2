<?php

namespace TIG\PostNL\Config\Source\Globalpack;

use Magento\Framework\Option\ArrayInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class ProductAttributes implements ArrayInterface
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
