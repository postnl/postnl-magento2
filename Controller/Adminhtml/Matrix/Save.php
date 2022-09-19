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
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Service\MatrixGrid\ErrorHandler;
use TIG\PostNL\Service\Validation\Factory;

class Save extends Action
{
    /** @var MatrixrateRepository  */
    protected $matrixrateRepository;

    /** @var Factory  */
    protected $_validator;

    /** @var ErrorHandler  */
    protected $_errorHandler;

    /**
     * @param Context               $context
     * @param MatrixrateRepository  $matrixrateRepository
     * @param Factory               $validator
     * @param ErrorHandler          $errorHandler
     */
    public function __construct(
        Context              $context,
        MatrixrateRepository $matrixrateRepository,
        Factory              $validator,
        ErrorHandler         $errorHandler
    ) {
        parent::__construct($context);
        $this->matrixrateRepository = $matrixrateRepository;
        $this->_validator           = $validator;
        $this->_errorHandler        = $errorHandler;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data  = $this->getRequest()->getPostValue();
        $model = $this->matrixrateRepository->create();

        try {
            foreach ($data['country_id'] as $countryCode) {
                // validate the data and catch the error's
                $validatedArray = $this->_errorHandler->process($data, $countryCode);

                if (!$validatedArray) {
                    $this->showErrors();
                    $this->_redirect($this->_redirect->getRefererUrl());
                    return;
                }

                $model->addData($validatedArray);
                $this->matrixrateRepository->save($model);
                $model->unsetData();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_redirect($this->_redirect->getRefererUrl());
            return;
        }

        $this->messageManager->addSuccessMessage(__('Data inserted successfully!'));
        $this->_redirect('*/*/index');
    }

    /**
     * Add error messages.
     *
     * @return void
     */
    public function showErrors()
    {
        $errorArray = $this->_errorHandler->getErrors();

        foreach ($errorArray as $error) {
            $this->messageManager->addErrorMessage($error);
        }
    }
}
