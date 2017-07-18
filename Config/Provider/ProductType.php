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

class ProductType extends AbstractSource
{
    const POSTNL_PRODUCT_TYPE        = 'postnl_product_type';
    const PRODUCT_TYPE_EXTRA_AT_HOME = 'extra_at_home';
    const PRODUCT_TYPE_REGULAR       = 'regular';
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
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

        return $options;
    }

    /**
     * @return array
     */
    public function getAllTypes()
    {
        return [
            self::PRODUCT_TYPE_EXTRA_AT_HOME,
            self::PRODUCT_TYPE_REGULAR
        ];
    }
}
