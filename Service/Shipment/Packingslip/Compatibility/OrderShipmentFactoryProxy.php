<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\ObjectManagerInterface;

// @codingStandardsIgnoreFile
class OrderShipmentFactoryProxy
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
     * @return \Fooman\PdfCustomiser\Block\OrderShipmentFactory
     */
    private function getSubject()
    {
        if (!$this->subject) {
            $this->subject = $this->objectManager->get(\Fooman\PdfCustomiser\Block\OrderShipmentFactory::class);
        }
        return $this->subject;
    }

    /**
     * @param array $data
     *
     * @return \Fooman\PdfCustomiser\Block\OrderShipment
     */
    public function create(array $data = [])
    {
        return $this->getSubject()->create($data);
    }
}
