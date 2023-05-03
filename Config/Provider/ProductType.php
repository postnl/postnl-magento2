<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Service\Shipping\BoxablePackets;

class ProductType extends AbstractSource
{
    const POSTNL_PRODUCT_TYPE            = 'postnl_product_type';
    const PRODUCT_TYPE_EXTRA_AT_HOME     = 'extra_at_home';
    const PRODUCT_TYPE_REGULAR           = 'regular';
    const PRODUCT_TYPE_LETTERBOX_PACKAGE = 'letterbox_package';
    const PRODUCT_TYPE_BOXABLE_PACKET    = 'boxable_packets';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @var BoxablePackets
     */
    private $boxablePackets;

    /**
     * @param ShippingOptions  $shippingOptions
     * @param LetterboxPackage $letterboxPackage
     * @param BoxablePackets $boxablePackets
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        LetterboxPackage $letterboxPackage,
        BoxablePackets $boxablePackets
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->letterboxPackage = $letterboxPackage;
        $this->boxablePackets = $boxablePackets;
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

        if ($this->shippingOptions->isLetterboxPackageActive() || $this->shippingOptions->isBoxablePacketsActive() ) {
            $options[] = ['value' => self::PRODUCT_TYPE_LETTERBOX_PACKAGE, 'label' => __('(International) Letterbox Package')];
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

        if ($this->boxablePackets->isBoxablePacket($items, false)) {
            $types[] = self::PRODUCT_TYPE_BOXABLE_PACKET;
        }

        return $types;
    }
}
