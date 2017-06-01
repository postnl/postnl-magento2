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
namespace TIG\PostNL\Block\Adminhtml\Config\Carrier\Matrixrate;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class Export extends AbstractElement
{
    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @param Factory           $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper           $escaper
     * @param UrlInterface      $backendUrl
     * @param array             $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $backendUrl,
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
        $form = $this->getForm()->getParent();
        $layout = $form->getLayout();

        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $layout->createBlock('Magento\Backend\Block\Widget\Button');
        $buttonBlockRequest = $buttonBlock->getRequest();

        $params = ['website' => $buttonBlockRequest->getParam('website')];
        $url = $this->backendUrl->getUrl("postnl/carrier_matrixrate/export", $params);
        $data = [
            'label' => __('Export CSV'),
            'onclick' => "setLocation('" . $url . "' )",
            'class' => '',
        ];
        $buttonBlock->setData($data);

        $html = $buttonBlock->toHtml();
        return $html;
    }
}
