<?php

namespace TIG\PostNL\Service\Options;

use TIG\PostNL\Config\Provider\ProductType;
use TIG\PostNL\Service\Order\ProductInfo;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;

class ItemsToOption
{
    private $typeToOption = [
        ProductType::PRODUCT_TYPE_EXTRA_AT_HOME     => ProductInfo::OPTION_EXTRAATHOME,
        ProductType::PRODUCT_TYPE_LETTERBOX_PACKAGE => ProductInfo::OPTION_LETTERBOX_PACKAGE,
        ProductType::PRODUCT_TYPE_REGULAR           => '',
    ];

    /**
     * Priority of types, 1 is highest.
     * @var array
     */
    private $priority = [
        ProductType::PRODUCT_TYPE_EXTRA_AT_HOME     => 1,
        ProductType::PRODUCT_TYPE_LETTERBOX_PACKAGE => 2,
        ProductType::PRODUCT_TYPE_REGULAR           => 3,
    ];

    /**
     * @var string
     */
    private $currentType = ProductType::PRODUCT_TYPE_REGULAR;

    /**
     * @var ProductDictionary
     */
    private $productDictionary;

    /**
     * @var ProductType
     */
    private $productTypes;

    /**
     * @param ProductDictionary $productDictionary
     * @param ProductType       $type
     */
    public function __construct(
        ProductDictionary $productDictionary,
        ProductType $type
    ) {
        $this->productDictionary = $productDictionary;
        $this->productTypes = $type;
    }

    /**
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * @return string
     */
    public function get($items)
    {
        foreach ($this->productTypes->getAllTypes($items) as $type) {
            $products = $this->productDictionary->get($items, [$type]);
            $this->updateCurrentType($products, $type);
        }

        return $this->typeToOption[$this->currentType];
    }

    /**
     * @param $products
     * @param $type
     */
    private function updateCurrentType($products, $type)
    {
        if (empty($products)) {
            $type = $this->currentType;
        }

        $currentPriority = $this->priority[$this->currentType];
        $newPriority     = $this->priority[$type];

        if ($newPriority <= $currentPriority) {
            $this->currentType = $type;
        }
    }

    /**
     * @param $quote
     *
     * @return string
     */
    public function getFromQuote($quote)
    {
        $items = $quote->getAllItems();

        return $this->get($items);
    }
}
