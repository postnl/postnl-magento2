<?php


namespace TIG\PostNL\Controller\Adminhtml\Matrix;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;

class Delete  extends  Action
{
    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;

    /**
     * @param Context               $context
     * @param MatrixrateRepository  $matrixrateRepository
     */
    public function __construct(
        Context $context,
        MatrixrateRepository $matrixrateRepository
    ) {
        $this->matrixrateRepository = $matrixrateRepository;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if($id){
            $model = $this->matrixrateRepository->create();
            $model->load($id);

            if($model->getEntityId()){
                try{
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('The record has been delete successfully'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went to wrong while Delete'));
                }
                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addErrorMessage(__('The Record does not exits'));
        return $resultRedirect->setPath('*/*/index');
    }

}
