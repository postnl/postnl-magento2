<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\ObjectManagerInterface;

// @codingStandardsIgnoreFile
class ShipmentFactoryProxy
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Fooman\PdfCustomiser\Block\OrderShipmentFactory
     */
    private $subject;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Fooman\PdfCustomiser\Block\ShipmentFactory
     */
    private function getSubject()
    {
        if (!$this->subject) {
            $this->subject = $this->objectManager->get(\Fooman\PdfCustomiser\Block\ShipmentFactory\Proxy::class);
        }
        return $this->subject;
    }

    /**
     * @param array $data
     *
     * @return \Fooman\PdfCustomiser\Block\Shipment
     */
    public function create(array $data = [])
    {
        return $this->getSubject()->create($data);
    }
}
