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
                    $this->messageManager->addSuccessMessage(__('The record has been deleted successfully'));
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went wrong while deleting'));
                }
                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addErrorMessage(__('The record does not exits'));
        return $resultRedirect->setPath('*/*/index');
    }

}
