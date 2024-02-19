<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;

class Delete extends Action
{
    /**
     * @var MatrixrateRepository
     */
    private $matrixrateRepository;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @param Context              $context
     * @param MatrixrateRepository $matrixrateRepository
     * @param Collection           $collection
     */
    public function __construct(
        Context              $context,
        MatrixrateRepository $matrixrateRepository,
        Collection           $collection,
        Log                  $logger
    ) {
        $this->matrixrateRepository = $matrixrateRepository;
        $this->collection           = $collection;
        $this->logger               = $logger;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id             = $this->getRequest()->getParam('id');

        if ($id) {
            $model = $this->collection->getItemById($id);

            if ($model->getEntityId()) {
                try {
                    $this->matrixrateRepository->delete($model);
                    $this->messageManager->addSuccessMessage(__('The record has been deleted successfully'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went wrong while deleting'));
                    $this->logger->error($e->getMessage());
                }

                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addErrorMessage(__('The record does not exists'));

        return $resultRedirect->setPath('*/*/index');
    }
}
