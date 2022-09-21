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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Model\Carrier\Matrixrate;
use TIG\PostNL\Model\Carrier\MatrixrateFactory;

class MassDelete extends Action
{
    /** @var Registry|null  */
    protected $_coreRegistry = null;

    /** @var PageFactory  */
    protected $resultPageFactory;

    /** @var Matrixrate */
    protected $matrixrateCollection;

    /** @var Filter */
    private $filter;

    /** @var MatrixrateFactory  */
    private $matrixrateFactory;

    /**
     * @param Context           $context
     * @param PageFactory       $resultPageFactory
     * @param Registry          $registry
     * @param Filter            $filter
     * @param Matrixrate        $matrixrateCollection
     * @param MatrixrateFactory $matrixrateFactory
     */
    public function __construct(
        Context           $context,
        PageFactory       $resultPageFactory,
        Registry          $registry,
        Filter            $filter,
        Matrixrate        $matrixrateCollection,
        MatrixrateFactory $matrixrateFactory
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_coreRegistry        = $registry;
        $this->matrixrateCollection = $matrixrateCollection;
        $this->filter               = $filter;
        $this->matrixrateFactory    = $matrixrateFactory;
        parent::__construct($context);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->matrixrateFactory->create()->getCollection());
        $count      = 0;

        foreach ($collection as $child) {
            $child->delete();
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));
        $this->_redirect('*/*/index');
    }
}
