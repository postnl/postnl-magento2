<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Matrixrate;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Renderer\Import as ImportBlock;

class Import extends AbstractElement
{
    /**
     * @var ImportBlock
     */
    private $importBlock;

    /**
     * Import constructor.
     *
     * @param Factory           $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper           $escaper
     * @param ImportBlock       $importBlock
     * @param array             $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        ImportBlock $importBlock,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->importBlock = $importBlock;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->setType('file');
        $this->importBlock->updateTimeConditionName($this->getName());

        $html = $this->importBlock->toHtml();
        $html .= parent::getElementHtml();

        return $html;
    }
}
