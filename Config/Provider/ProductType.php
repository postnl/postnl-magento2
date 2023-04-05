<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use TIG\PostNL\Service\Shipping\LetterboxPackage;

class ProductType extends AbstractSource
{
    const POSTNL_PRODUCT_TYPE            = 'postnl_product_type';
    const PRODUCT_TYPE_EXTRA_AT_HOME     = 'extra_at_home';
    const PRODUCT_TYPE_REGULAR           = 'regular';
    const PRODUCT_TYPE_LETTERBOX_PACKAGE = 'letterbox_package';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @param ShippingOptions  $shippingOptions
     * @param LetterboxPackage $letterboxPackage
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        LetterboxPackage $letterboxPackage
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->letterboxPackage = $letterboxPackage;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $options = [
            // @codingStandardsIgnoreLine
            ['value' => self::PRODUCT_TYPE_REGULAR, 'label' => __('Regular')],
            // @codingStandardsIgnoreLine
        ];

        if ($this->shippingOptions->isExtraAtHomeActive()) {
            $options[] = ['value' => self::PRODUCT_TYPE_EXTRA_AT_HOME, 'label' => __('Extra@Home')];
        }

        if ($this->shippingOptions->isLetterboxPackageActive()) {
            $options[] = ['value' => self::PRODUCT_TYPE_LETTERBOX_PACKAGE, 'label' => __('Letterbox Package')];
        }

        return $options;
    }

    /**
     * @param $items
     *
     * @return array
     */
    public function getAllTypes($items)
    {
        $types = [
            self::PRODUCT_TYPE_EXTRA_AT_HOME,
            self::PRODUCT_TYPE_REGULAR
        ];

        if ($this->letterboxPackage->isLetterboxPackage($items, false)) {
            $types[] = self::PRODUCT_TYPE_LETTERBOX_PACKAGE;
        }

        return $types;
    }
}
