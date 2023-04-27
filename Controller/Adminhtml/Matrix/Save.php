<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Service\MatrixGrid\ErrorHandler;

class Save extends Action
{
    /** @var MatrixrateRepository  */
    protected $matrixrateRepository;

    /** @var ErrorHandler  */
    protected $_errorHandler;

    /**
     * @param Context               $context
     * @param MatrixrateRepository  $matrixrateRepository
     * @param ErrorHandler          $errorHandler
     */
    public function __construct(
        Context              $context,
        MatrixrateRepository $matrixrateRepository,
        ErrorHandler         $errorHandler
    ) {
        parent::__construct($context);
        $this->matrixrateRepository = $matrixrateRepository;
        $this->_errorHandler        = $errorHandler;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data  = $this->getRequest()->getPostValue();
        $model = $this->matrixrateRepository->create();

        try {
            foreach ($data['country_id'] as $countryCode) {
                $model->addData($data);
                $model->setData('destiny_country_id', $countryCode);
                $model->unsetData('country_id');
                $this->matrixrateRepository->save($model);
                $model->unsetData();
            }
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        }

        $this->messageManager->addSuccessMessage(__('Data inserted successfully!'));
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
