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
namespace TIG\PostNL\Service\Options;

use TIG\PostNL\Model\Product\Attribute\Source\Type;
use TIG\PostNL\Service\Order\ProductCode;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use TIG\PostNL\Service\Wrapper\QuoteInterface;

class ItemsToOption
{
    private $typeToOption = [
        Type::PRODUCT_TYPE_EXTRA_AT_HOME => ProductCode::OPTION_EXTRAATHOME,
        Type::PRODUCT_TYPE_REGULAR       => '',
    ];

    /**
     * Priority of types, 1 is highest.
     * @var array
     */
    private $priority = [
        Type::PRODUCT_TYPE_EXTRA_AT_HOME => 1,
        Type::PRODUCT_TYPE_REGULAR       => 2,
    ];

    /**
     * @var string
     */
    private $currentType = Type::PRODUCT_TYPE_REGULAR;

    /**
     * @var ProductDictionary
     */
    private $productDictionary;

    /**
     * @var Type
     */
    private $productTypes;

    /**
     * @var QuoteInterface
     */
    private $quote;

    /**
     * @param QuoteInterface    $quote
     * @param ProductDictionary $productDictionary
     * @param Type              $type
     */
    public function __construct(
        QuoteInterface $quote,
        ProductDictionary $productDictionary,
        Type $type
    ) {
        $this->productDictionary = $productDictionary;
        $this->productTypes = $type;
        $this->quote = $quote;
    }

    /**
     * @param ShipmentItemInterface[]|OrderItemInterface[]|QuoteItem[] $items
     *
     * @return string
     */
    public function get($items)
    {
        foreach ($this->productTypes->getAllTypes() as $type) {
            $products = $this->productDictionary->get($items, [$type]);
            $this->setCurrentType($products, $type);
        }

        return $this->typeToOption[$this->currentType];
    }

    /**
     * @param $products
     * @param $type
     */
    private function setCurrentType($products, $type)
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
     * @return string
     */
    public function getFromQuote()
    {
        return $this->get($this->quote->getAllItems());
    }
}
