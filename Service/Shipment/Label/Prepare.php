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
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterface;

class Prepare
{
    /**
     * @var TypeInterface[]
     */
    private $types;
    /**
     * @var Type
     */
    private $typeConverter;

    /**
     * @param Type  $typeConverter
     * @param array $types
     *
     * @throws PostNLException
     */
    public function __construct(
        Type $typeConverter,
        $types = []
    ) {
        $this->typeConverter = $typeConverter;
        $this->types = $types;

        foreach ($types as $name => $instance) {
            $this->validateType($name, $instance);
        }
    }

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return \FPDF
     * @throws PostNLException
     */
    public function label(ShipmentLabelInterface $label)
    {
        $shipment = $label->getShipment();
        $normalizedShipment = strtolower($this->typeConverter->get($shipment));

        $instance = $this->types['domestic'];
        if (array_key_exists($normalizedShipment, $this->types)) {
            $instance = $this->types[$normalizedShipment];
        }

        $result = $instance->process($label);
        $instance->cleanup();

        return $result;
    }

    /**
     * @param $name
     * @param $instance
     *
     * @throws PostNLException
     */
    private function validateType($name, $instance)
    {
        if (!$instance instanceof TypeInterface) {
            throw new PostNLException(
                // @codingStandardsIgnoreLine
                __($name . ' is not an instance of ' . TypeInterface::class)
            );
        }
    }
}
