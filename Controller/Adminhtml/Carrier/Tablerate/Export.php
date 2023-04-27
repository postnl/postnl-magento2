<?php

namespace TIG\PostNL\Controller\Adminhtml\Carrier\Tablerate;

use Magento\Backend\App\Action\Context;
use Magento\Config\Controller\Adminhtml\System\AbstractConfig;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;

use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Grid as PostnlGrid;

class Export extends AbstractConfig
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     * @param FileFactory $fileFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->fileFactory = $fileFactory;

        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'tablerates.csv';
        $viewLayout = $this->_view->getLayout();

        /** @var $gridBlock PostnlGrid */
        $gridBlock = $viewLayout->createBlock(PostnlGrid::class);

        $website = $this->storeManager->getWebsite($this->getRequest()->getParam('website'));
        $conditionName = $website->getConfig('carriers/tig_postnl/condition_name');

        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        }

        $gridBlock->setWebsiteId($website->getId());
        $gridBlock->setConditionName($conditionName);
        $content = $gridBlock->getCsvFile();

        return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
