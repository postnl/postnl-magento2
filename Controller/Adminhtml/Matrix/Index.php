<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /** @var bool|PageFactory  */
    protected $resultPageFactory = false;

    /**
     * @param Context       $context
     * @param PageFactory   $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::sales');
        $resultPage->getConfig()->getTitle()->prepend((__('Matrix rates')));


        return $resultPage;
    }


}
