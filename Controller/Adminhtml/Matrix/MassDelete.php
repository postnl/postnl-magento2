<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Model\Carrier\Matrixrate;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\CollectionFactory;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;

class MassDelete extends Action
{
    private MatrixrateRepositoryInterface $matrixrateRepository;

    /** @var Registry|null  */
    protected $_coreRegistry = null;

    /** @var PageFactory  */
    protected $resultPageFactory;

    /** @var Matrixrate */
    protected $matrixrateCollection;

    /** @var Filter */
    private $filter;

    /** @var CollectionFactory  */
    private $matrixrateCollectionFactory;

    /**
     * @param Context           $context
     * @param PageFactory       $resultPageFactory
     * @param Registry          $registry
     * @param Filter            $filter
     * @param Matrixrate        $matrixrateCollection
     * @param CollectionFactory $matrixrateCollectionFactory
     */
    public function __construct(
        Context           $context,
        PageFactory       $resultPageFactory,
        Registry          $registry,
        Filter            $filter,
        Matrixrate        $matrixrateCollection,
        CollectionFactory $matrixrateCollectionFactory,
        MatrixrateRepositoryInterface $matrixrateRepository
    ) {
        $this->matrixrateRepository = $matrixrateRepository;

        $this->resultPageFactory    = $resultPageFactory;
        $this->_coreRegistry        = $registry;
        $this->matrixrateCollection = $matrixrateCollection;
        $this->filter               = $filter;
        $this->matrixrateCollectionFactory    = $matrixrateCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->matrixrateCollectionFactory->create());
        $count      = 0;

        foreach ($collection as $child) {
            $this->matrixrateRepository->delete($child);
            $count++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));
        $this->_redirect('*/*/index');
    }
}
