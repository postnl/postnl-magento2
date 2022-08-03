<?php


namespace TIG\PostNL\Controller\Adminhtml\Matrix;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;

class Delete  extends  Action
{
    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;


    public function __construct(
        Context $context,
        MatrixrateRepository $matrixrateRepository
    ) {
        $this->matrixrateRepository = $matrixrateRepository;
        parent::__construct($context);

    }

    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        /**
         * Get Record id from Url parameters
         */
        $id = $this->getRequest()->getParam('id');

        if($id){
            $model = $this->matrixrateRepository->create();
            $model->load($id);
            /**
             * If getId() has value then means record exits
             */
            if($model->getEntityId()){

                try{
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('The record has been delete successfully'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went to wrong while Delete'));
                }

                // after delete Record ,return to Listing page
                return $resultRedirect->setPath('*/*/index');
            }

        }
        $this->messageManager->addErrorMessage(__('The Record does not exits'));
        //  Return to Listing page
        return $resultRedirect->setPath('*/*/index');
    }

}
