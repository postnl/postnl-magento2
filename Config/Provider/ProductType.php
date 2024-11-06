<?php

namespace TIG\PostNL\Config\Provider;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use TIG\PostNL\Service\Shipping\InternationalPacket;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Service\Shipping\BoxablePackets;

class ProductType extends AbstractSource
{
    const POSTNL_PRODUCT_TYPE            = 'postnl_product_type';
    const PRODUCT_TYPE_EXTRA_AT_HOME     = 'extra_at_home';
    const PRODUCT_TYPE_REGULAR           = 'regular';
    const PRODUCT_TYPE_LETTERBOX_PACKAGE = 'letterbox_package';
    const PRODUCT_TYPE_INTERNATIONAL_PACKET = 'international_packet';
    const PRODUCT_TYPE_BOXABLE_PACKET    = 'boxable_packets';

    private ShippingOptions $shippingOptions;

    private LetterboxPackage $letterboxPackage;

    private BoxablePackets $boxablePackets;

    private InternationalPacket $internationalPacket;

    public function __construct(
        ShippingOptions $shippingOptions,
        LetterboxPackage $letterboxPackage,
        BoxablePackets $boxablePackets,
        InternationalPacket $internationalPacket
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->letterboxPackage = $letterboxPackage;
        $this->boxablePackets = $boxablePackets;
        $this->internationalPacket = $internationalPacket;
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

        $options[] = ['value' => self::PRODUCT_TYPE_LETTERBOX_PACKAGE, 'label' => __('(International) Letterbox Package')];

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

        if ($this->letterboxPackage->isLetterboxPackage($items)) {
            $types[] = self::PRODUCT_TYPE_LETTERBOX_PACKAGE;
        }

        if ($this->internationalPacket->canFixInTheBox($items)) {
            $types[] = self::PRODUCT_TYPE_INTERNATIONAL_PACKET;
        }

        if ($this->boxablePackets->canFixInTheBox($items)) {
            $types[] = self::PRODUCT_TYPE_BOXABLE_PACKET;
        }

        return $types;
    }
}
