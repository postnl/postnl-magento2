<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;



use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;

class Edit extends  Action
{

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;

    /**
     *
     * Add Acl Resource id For Permission at admin section
     */
    const ADMIN_RESOURCE ="StackExchange_Example::example_edit";

    public function __construct(
        Context $context,
        MatrixrateRepository $matrixrateRepository,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->matrixrateRepository = $matrixrateRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function execute() {
//
//        $this->_view->loadLayout();
//        $this->_view->renderLayout();
//
//        $contact = $this->matrixrateRepository->create();
////        var_dump($this->getRequest()->getParams());
////        die();
//
//        $contactDatas = $contact->load($this->getRequest()->getParam('id'));
////        $contactDatas = $this->getRequest()->getParam('entity_id');
//        if(is_array($contactDatas)) {
//            $contact = $this->matrixrateRepository->create();
//
//            $contact->setData($contactDatas);
//            $contact->save();
//            $resultRedirect = $this->resultRedirectFactory->create();
//            return $resultRedirect->setPath('*/*/index');
//        }
//        $resultRedirect = $this->resultRedirectFactory->create();
//        return $resultRedirect->setPath('*/*/index');


        /**
         * init Model using Model Factory
         */
        $model = $this->matrixrateRepository->create();
        /**
         * for  update a row data, we need  primary  field value
         * which URL param "example_id" = Database example table "id" field
         */
        $id = $this->getRequest()->getParam('id');
        if($id){
            /**
             * Load a record data from data using model
             */
            $model->load($id);
            $model->getCollection();


            /**
             * Redirect to listing page if a record does not exit at database
             * with request parameter
             */
            if(!$model->getEntityId()){
                $resultRedirect =  $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/index');
            }

        }
        /**
         * Save Model Data to a registry variable for future purpose
         * Variable name is user defined
         */
        $this->registry->register('matrixrate_edit_grid_item',$model);

        $resultPage =$this->resultPageFactory->create();

        $resultPage->getConfig()->setKeywords(__('Edit Page'));


        /**
         * Set Page title
         */

        $resultPage->getConfig()->getTitle()->prepend('PostNL Module');

        $pageTitlePrefix = __('Edit Page for Entity number %1',

            $model->getEntityId()?$model->getEntityId(): __('New entry')
        );
        $resultPage->getConfig()->getTitle()->prepend($pageTitlePrefix);
        return $resultPage;

    }

}
