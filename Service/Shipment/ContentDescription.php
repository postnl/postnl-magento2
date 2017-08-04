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
namespace TIG\PostNL\Service\Shipment;

use Magento\Sales\Api\Data\ShipmentInterface as MagentoShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Config\Provider\ProductType as PostNLType;
use TIG\PostNL\Service\Options\ProductDictionary;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;

class ContentDescription
{
    const MAX_STRINGLENGTH = 35;

    private $productDictionary;

    /**
     * @param ProductDictionary $productDictionary
     */
    public function __construct(ProductDictionary $productDictionary)
    {
        $this->productDictionary = $productDictionary;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    public function get(ShipmentInterface $shipment)
    {
        /** @var MagentoShipmentInterface $magentoShipment */
        $magentoShipment = $shipment->getShipment();
        $items = $magentoShipment->getItems();

        if ($shipment->isExtraAtHome()) {
            $items = $this->productDictionary->get($items, [PostNLType::PRODUCT_TYPE_EXTRA_AT_HOME]);
        }

        return $this->formatDescription($items);
    }

    /**
     * @param ProductInterface[]|ShipmentItemInterface[] $items
     *
     * @return string
     */
    private function formatDescription($items)
    {
        $desc = $this->getProductsListedAsString($items);
        return strlen($desc) > self::MAX_STRINGLENGTH ? substr($desc, 0, self::MAX_STRINGLENGTH - 3).'...' : $desc;
    }

    /**
     * @param ProductInterface[]|ShipmentItemInterface[] $items
     *
     * @return string
     */
    private function getProductsListedAsString($items)
    {
        $description = '';
        foreach ($items as $item) {
            $description.= ' '.$item->getName(). ',';
        }

        return rtrim(trim($description), ',');
    }
}
