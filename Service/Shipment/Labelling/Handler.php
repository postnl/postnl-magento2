<?php

namespace TIG\PostNL\Service\Shipment\Labelling;

use TIG\PostNL\Service\Shipment\Labelling\Handler\HandlerInterfaceFactory;
use TIG\PostNL\Service\Shipment\Labelling\Handler\GlobalpackFactory;
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
     * @var GlobalpackFactory
     */
    private $globalpackFactory;

    /**
     * Handler constructor.
     *
     * @param Type              $type
     * @param GlobalpackFactory $globalpackFactory
     * @param array             $handlers
     */
    public function __construct(
        Type $type,
        GlobalpackFactory $globalpackFactory,
        $handlers = []
    ) {
        $this->typeConverter = $type;
        $this->globalpackFactory = $globalpackFactory;
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
            $labelContents[] = ['Content' => $label->Content, 'Type' => $label->Labeltype];
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
