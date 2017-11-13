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
namespace TIG\PostNL\Block\Adminhtml\Renderer;

use TIG\PostNL\Config\Source\Options\ProductOptions;

class ShipmentType
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * ShipmentType constructor.
     *
     * @param ProductOptions $productOptions
     */
    public function __construct(
        ProductOptions $productOptions
    ) {
        $this->productOptions = $productOptions;
    }

    /**
     * @param $type
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    private function getLabel($type)
    {
        $label = '';
        $comment = '';

        switch ($type) {
            case 'Daytime':
                $label = __('Domestic');
                break;
            case 'Evening':
                $label = __('Domestic');
                $comment = __('Evening');
                break;
            case 'ExtraAtHome':
            case 'Extra@Home':
                $label = __('Extra@Home');
                break;
            case 'Sunday':
                $label = __('Sunday');
                break;
            case 'PG':
                $label = __('Post office');
                break;
            case 'PGE':
                $label = __('Post office');
                $comment = __('Early morning pickup');
                break;
            case 'EPS':
                $label = __('EPS');
                break;
        }

        return [
            'label' => $label,
            'comment' => $comment,
        ];
    }
    // @codingStandardsIgnoreStop

    /**
     * @param $type
     *
     * @return string
     */
    public function render($type)
    {
        $type = $this->getLabel($type);
        $output = (string)$type['label'];

        if ($type['comment']) {
            $output .= '<br><em>' . $type['comment'] . '</em>';
        }

        return $output;
    }
}
