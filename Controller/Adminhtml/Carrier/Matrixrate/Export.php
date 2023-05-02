<?php

namespace TIG\PostNL\Controller\Adminhtml\Carrier\Matrixrate;

use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Store\Model\StoreManagerInterface;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;
use TIG\PostNL\Service\Export\Csv\Matrixrate;

class Export extends Action
{
    /**
     * @var Matrixrate
     */
    private $export;

    /**
     * @var MatrixrateRepositoryInterface
     */
    private $matrixrateRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * Export constructor.
     *
     * @param Action\Context                $context
     * @param StoreManagerInterface         $storeManager
     * @param FileFactory                   $fileFactory
     * @param Matrixrate                    $export
     * @param MatrixrateRepositoryInterface $matrixrateRepository
     */
    public function __construct(
        Action\Context $context,
        StoreManagerInterface $storeManager,
        FileFactory $fileFactory,
        Matrixrate $export,
        MatrixrateRepositoryInterface $matrixrateRepository
    ) {
        parent::__construct($context);

        $this->export = $export;
        $this->matrixrateRepository = $matrixrateRepository;
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $website = $this->storeManager->getWebsite($this->getRequest()->getParam('website'));
        $collection = $this->matrixrateRepository->getByWebsiteId($website->getId());

        $result = $this->export->build($collection);

        return $this->fileFactory->create('matrixrate.csv', $result);
    }
}
