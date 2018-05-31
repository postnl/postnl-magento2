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
namespace TIG\PostNL\Service\Shipment\Labelling;

use TIG\PostNL\Service\Shipment\Labelling\Handler\HandlerInterfaceFactory;
use TIG\PostNL\Service\Shipment\Labelling\Handler\HandlerInterface;
use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Exception as PostNLException;

class Handler
{
    /**
     * @var HandlerInterfaceFactory[]
     */
    private $handlers = [];

    /**
     * @var bool
     */
    private $isValidated = false;

    /**
     * @var Type
     */
    private $typeConverter;

    /**
     * Handler constructor.
     *
     * @param Type $type
     * @param array $handlers
     */
    public function __construct(
        Type $type,
        $handlers = []
    ) {
        $this->typeConverter = $type;
        $this->handlers = $handlers;
    }

    /**
     * @param $shipment
     * @param $labels
     *
     * @return array
     */
    public function handle($shipment, $labels)
    {
        $this->validateHandlers();
        $normalizedShipment = strtolower($this->typeConverter->get($shipment));

        $instanceFactory = false;
        if (array_key_exists($normalizedShipment, $this->handlers)) {
            $instanceFactory = $this->handlers[$normalizedShipment];
        }

        if (!$instanceFactory) {
            return ['type' => $normalizedShipment, 'labels' => $this->handleDefault($labels)];
        }

        /**
         * @var HandlerInterface $instance
         */
        $instance = $instanceFactory->create();
        // @codingStandardsIgnoreLine
        $result = $instance->format($labels);
        return ['type' => $normalizedShipment, 'labels' => [$result]];
    }

    /**
     * @param $labels
     *
     * @return array
     */
    private function handleDefault($labels)
    {
        $labelContents = [];
        foreach ($labels as $label) {
            $labelContents[] = $label->Content;
        }

        return $labelContents;
    }

    private function validateHandlers()
    {
        if ($this->isValidated) {
            return;
        }

        $this->isValidated = true;

        foreach ($this->handlers as $name => $instanceFactory) {
            $this->validateHandler($name, $instanceFactory);
        }
    }

    /**
     * @param $name
     * @param $instanceFactory
     *
     * @throws PostNLException
     */
    private function validateHandler($name, $instanceFactory)
    {
        $instance = $instanceFactory->create();

        if (!$instance instanceof HandlerInterface) {
            throw new PostNLException(
            // @codingStandardsIgnoreLine
                __($name . ' is not an instance of ' . HandlerInterface::class)
            );
        }
    }
}
