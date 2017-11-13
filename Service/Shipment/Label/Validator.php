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
namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;

class Validator
{
    /**
     * Removes all labels that are empty or not a string. If a shipment has no valid labels, the shipment will be
     * removed from the stack.
     *
     * @param $input
     * @return array
     */
    public function validate($input)
    {
        if (!is_array($input)) {
            return [];
        }

        $filtered = array_filter($input, [$this, 'filterInput']);

        return array_values($filtered);
    }

    /**
     * @param ShipmentLabelInterface|null $model
     *
     * @return bool
     */
    private function filterInput(ShipmentLabelInterface $model = null)
    {
        if ($model === null) {
            return false;
        }

        $label = $model->getLabel();

        if (!is_string($label) || empty($label)) {
            return false;
        }

        $start = substr($label, 0, strlen('invalid'));
        $start = strtolower($start);

        return $start != 'invalid';
    }
}
