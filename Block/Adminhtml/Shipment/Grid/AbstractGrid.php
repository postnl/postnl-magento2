<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Shipment\Grid;

use TIG\PostNL\Model\ShipmentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Model\Shipment as PostNLShipment;

abstract class AbstractGrid extends Column
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $ids = [];

    /**
     * Holds the loaded items.
     *
     * @var array
     */
    protected $models = [];

    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @param ContextInterface     $contextInterface
     * @param UiComponentFactory   $uiComponentFactory
     * @param ShipmentFactory      $shipmentFactory
     * @param array                $components
     * @param array                $data
     */
    public function __construct(
        ContextInterface $contextInterface,
        UiComponentFactory $uiComponentFactory,
        ShipmentFactory $shipmentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($contextInterface, $uiComponentFactory, $components, $data);

        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $this->items = $dataSource['data']['items'];

            $this->prepareData();
            $this->handleItems();

            $dataSource['data']['items'] = $this->items;
        }

        return $dataSource;
    }

    /**
     * Load all the needed data in only 1 query.
     */
    public function prepareData()
    {
        $ids = $this->collectIds();

        /** @var PostNLShipment $postnlShipment */
        $postnlShipment = $this->shipmentFactory->create();

        /** @var \TIG\PostNL\Model\ResourceModel\Shipment\Collection $collection */
        $collection = $postnlShipment->getCollection();
        $collection->addFieldToFilter('shipment_id', ['in' => $ids]);

        /** @var PostNLShipment $item */
        foreach ($collection as $item) {
            $this->models[$item->getShipmentId()] = $item;
        }
    }

    /**
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleItems()
    {
        foreach ($this->items as $index => $item) {
            $this->items[$index][$this->getData('name')] = $this->getCellContents($item);
        }
    }

    /**
     * @param object $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    abstract protected function getCellContents($item);

    /**
     * @param string $idColumn
     *
     * @return array
     */
    protected function collectIds($idColumn = 'entity_id')
    {
        foreach ($this->items as $item) {
            $this->ids[] = $item[$idColumn];
        }

        return $this->ids;
    }
}
