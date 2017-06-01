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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

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
