<?php

namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Matrixrate;

use Magento\Backend\Block\Widget\Button;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;


class MatrixButton extends AbstractElement
{
    /** @var UrlInterface */
    private $backendUrl;

    /**
     * Import constructor.
     *
     * @param Factory           $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper           $escaper
     * @param UrlInterface      $backendUrl
     * @param array             $data
     */
    public function __construct(
        Factory           $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper           $escaper,
        UrlInterface      $backendUrl,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $form   = $this->getForm()->getParent();
        $layout = $form->getLayout();

        /** @var Button $buttonBlock  */
        $buttonBlock = $layout->createBlock(Button::class);
        $url         = $this->backendUrl->getUrl("postnl/matrix/index");
        $data        = [
                     'label' => __('Show visual matrix'),
                     'onclick' => "setLocation('" . $url . "' )",
                     'class' => '',
                 ];
        $buttonBlock->setData($data);
        $html = $buttonBlock->toHtml();

        return $html;
    }
}
