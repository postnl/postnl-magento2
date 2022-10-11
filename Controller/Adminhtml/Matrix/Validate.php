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

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Variable\Controller\Adminhtml\System\Variable;
use TIG\PostNL\Service\MatrixGrid\ErrorHandler;

class Validate extends Variable
{
    /** @var ErrorHandler  */
    protected $_errorHandler;

    /**
     * @param Context        $context
     * @param Registry       $coreRegistry
     * @param ForwardFactory $resultForwardFactory
     * @param JsonFactory    $resultJsonFactory
     * @param PageFactory    $resultPageFactory
     * @param LayoutFactory  $layoutFactory
     * @param ErrorHandler   $errorHandler
     */
    public function __construct(
        Context        $context,
        Registry       $coreRegistry,
        ForwardFactory $resultForwardFactory,
        JsonFactory    $resultJsonFactory,
        PageFactory    $resultPageFactory,
        LayoutFactory  $layoutFactory,
        ErrorHandler   $errorHandler
    ) {
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultJsonFactory, $resultPageFactory, $layoutFactory);
        $this->_errorHandler = $errorHandler;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject(['error' => false]);
        $data     = $this->getRequest()->getPostValue();

        foreach ($data['country_id'] as $countryCode) {
            // validate the data and catch the error's
            $validatedArray = $this->_errorHandler->process($data, $countryCode);

            if ($validatedArray === false) {
                $this->getErrorMessages();
                $layout = $this->layoutFactory->create();
                $layout->initMessages();
                $response->setError(true);
                $response->setHtmlMessage($layout->getMessagesBlock()->getGroupedHtml());
            }
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response->toArray());
    }

    public function getErrorMessages()
    {
        foreach ($this->_errorHandler->getErrors() as $error) {
            $this->messageManager->addErrorMessage((string)$error);
        }
    }
}
