<?php

namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Service\Shipment\Label\Type\DomesticFactory;
use TIG\PostNL\Service\Shipment\Label\Type\EPSFactory;
use TIG\PostNL\Service\Shipment\Label\Type\GlobalPackFactory;
use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterface;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterfaceFactory;

class Prepare
{
    /**
     * @var TypeInterfaceFactory[]
     */
    private $types;

    /**
     * @var Type
     */
    private $typeConverter;

    /**
     * @var bool
     */
    private $isValidated = false;

    /**
     * @var DomesticFactory
     */
    private $domesticFactory;

    /**
     * @var EPSFactory
     */
    private $epsFactory;

    /**
     * @var GlobalPackFactory
     */
    private $globalPackFactory;

    /**
     * @param Type  $typeConverter
     * @param DomesticFactory $domesticFactory
     * @param EPSFactory $epsFactory
     * @param GlobalPackFactory $globalPackFactory
     * @param array $types
     *
     * @throws PostNLException
     */
    public function __construct(
        Type $typeConverter,
        DomesticFactory $domesticFactory,
        EPSFactory $epsFactory,
        GlobalPackFactory $globalPackFactory,
        $types = []
    ) {
        $this->typeConverter = $typeConverter;
        $this->domesticFactory = $domesticFactory;
        $this->epsFactory = $epsFactory;
        $this->globalPackFactory = $globalPackFactory;
        $this->types = $types;
    }

    /**
     * @param ShipmentLabelInterface $label
     *
     * @return array
     * @throws PostNLException
     */
    public function label(ShipmentLabelInterface $label)
    {
        $this->validateTypes();

        $shipment = $label->getShipment();
        $normalizedShipment = strtolower($this->typeConverter->get($shipment));

        $instanceFactory = $this->types['domestic'];
        if (array_key_exists($normalizedShipment, $this->types)) {
            $instanceFactory = $this->types[$normalizedShipment];
        }

        /** @var TypeInterface $instance */
        $instance = $instanceFactory->create();

        $result = $instance->process($label);
        $instance->cleanup();

        return ['label' => $result, 'shipment' => $shipment];
    }

    /**
     * @param $name
     * @param $instanceFactory
     *
     * @throws PostNLException
     */
    private function validateType($name, $instanceFactory)
    {
        $instance = $instanceFactory->create();

        if (!$instance instanceof TypeInterface) {
            throw new PostNLException(
                // @codingStandardsIgnoreLine
                __($name . ' is not an instance of ' . TypeInterface::class)
            );
        }
    }

    /**
     *
     */
    private function validateTypes()
    {
        if ($this->isValidated) {
            return;
        }

        $this->isValidated = true;

        foreach ($this->types as $name => $instanceFactory) {
            $this->validateType($name, $instanceFactory);
        }
    }
}
