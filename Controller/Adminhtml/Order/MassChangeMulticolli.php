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
namespace TIG\PostNL\Controller\Adminhtml\Order;

use TIG\PostNL\Controller\Adminhtml\ToolbarAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class MassChangeMulticolli extends ToolbarAbstract
{
    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        OrderCollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $shipmentRepository, $orderRepository);

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection     = $this->collectionFactory->create();
        $collection     = $this->uiFilter->getCollection($collection);
        $newParcelCount = $this->getRequest()->getParam(self::PARCELCOUNT_PARAM_KEY);

        $this->changeMultiColli($collection, $newParcelCount);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @param $newParcelCount
     */
    private function changeMultiColli($collection, $newParcelCount)
    {
        foreach ($collection as $order) {
            $this->orderChangeParcelCount($order, $newParcelCount);
        }

        $this->handelErrors();

        $count = $this->getTotalCount($collection->getSize());
        if ($count > 0) {
            $this->messageManager->addSuccessMessage(
                __('Parcel count changed for %1 order(s)', $count)
            );
        }
    }
}
