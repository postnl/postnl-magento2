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
