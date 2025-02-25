<?php

namespace TIG\PostNL\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Service\Shipment\Label\Type\DomesticFactory;
use TIG\PostNL\Service\Shipment\Label\Type\EPSFactory;
use TIG\PostNL\Service\Shipment\Label\Type\GlobalPackFactory;
use TIG\PostNL\Service\Shipment\Label\Type\BoxablePacketsFactory;
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
     * @var BoxablePacketsFactory
     */
    private $boxablePacketsFactory;

    /**
     * @param Type  $typeConverter
     * @param DomesticFactory $domesticFactory
     * @param EPSFactory $epsFactory
     * @param GlobalPackFactory $globalPackFactory
     * @param BoxablePacketsFactory $boxablePacketsFactory
     * @param array $types
     *
     * @throws PostNLException
     */
    public function __construct(
        Type $typeConverter,
        DomesticFactory $domesticFactory,
        EPSFactory $epsFactory,
        GlobalPackFactory $globalPackFactory,
        BoxablePacketsFactory $boxablePacketsFactory,
        $types = []
    ) {
        $this->typeConverter = $typeConverter;
        $this->domesticFactory = $domesticFactory;
        $this->epsFactory = $epsFactory;
        $this->globalPackFactory = $globalPackFactory;
        $this->boxablePacketsFactory = $boxablePacketsFactory;
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
        $baseType = $this->typeConverter->get($shipment);
        $normalizedShipment = strtolower($baseType);
        $normalizedShipment = $this->adjustCountryOptions($shipment, $label, $normalizedShipment);

        $instanceFactory = $this->types['domestic'];
        if (array_key_exists($normalizedShipment, $this->types)) {
            $instanceFactory = $this->types[$normalizedShipment];
        }

        /** @var TypeInterface $instance */
        $instance = $instanceFactory->create();

        $result = $instance->process($label);
        $instance->cleanup();
        // Mark type for merged, so it knows how to merge data. Mostly sets GP/everything else as GP is specific.
        $result->shipmentType = $baseType;

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

    private function adjustCountryOptions(\TIG\PostNL\Api\Data\ShipmentInterface $shipment, ShipmentLabelInterface $label, string $normalizedShipment)
    {
        if ((int)$label->getProductCode() === 4907 && $label->getType() === 'pg') {
            return 'eps';
        }
        if ($shipment->getShipmentCountry() === 'BE' && $normalizedShipment === 'daytime' && $label->getReturnLabel()) {
            return 'eps';
        }
        if ((int)$label->getProductCode() === 4910) {
            return 'a4normal';
        }
        return $normalizedShipment;
    }
}
