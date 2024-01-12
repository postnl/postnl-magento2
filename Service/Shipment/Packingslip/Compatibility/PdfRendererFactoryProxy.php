<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\ObjectManagerInterface;

// @codingStandardsIgnoreFile
class PdfRendererFactoryProxy
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Fooman\PdfCore\Model\PdfRendererFactory
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
     * @return \Fooman\PdfCore\Model\PdfRendererFactory
     */
    private function getSubject()
    {
        if (!$this->subject) {
            $this->subject = $this->objectManager->get(\Fooman\PdfCore\Model\PdfRendererFactory::class);
        }
        return $this->subject;
    }

    /**
     * @param array $data
     *
     * @return \Fooman\PdfCore\Model\PdfRendererFactory
     */
    public function create(array $data = [])
    {
        return $this->getSubject()->create($data);
    }
}
