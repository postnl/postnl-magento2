<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\ObjectManagerInterface;

// @codingStandardsIgnoreFile
class DataHelperFactoryProxy
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Data
     */
    private $subject;

    /**
     * DataHelperFactoryProxy constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return mixed|Data
     */
    private function getSubject()
    {
        if (!$this->subject) {
            $this->subject = $this->objectManager->get(\Xtento\PdfCustomizer\Helper\DataFactory::class);
        }

        return $this->subject;
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->getSubject()->create($data);
    }
}
