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
        if ($this->letterboxPackage->isLetterboxPackage($items, false)) {
            return [
                self::PRODUCT_TYPE_LETTERBOX_PACKAGE,
                self::PRODUCT_TYPE_EXTRA_AT_HOME,
                self::PRODUCT_TYPE_REGULAR
            ];
        }

        return [
            self::PRODUCT_TYPE_EXTRA_AT_HOME,
            self::PRODUCT_TYPE_REGULAR
        ];
    }
}
