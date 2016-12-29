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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Model\ShipmentFactory;
use TIG\PostNL\Model\Shipment as PostNLShipment;

class ConfirmStatus extends AbstractGrid
{
    /**
     * @var ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * Holds the loaded items.
     *
     * @var array
     */
    protected $models = [];

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
     * @param $item
     *
     * @return string
     */
    protected function getCellContents($item)
    {
        $entity_id = $item['entity_id'];
        $confirmedAt = $this->getIsConfirmed($entity_id);

        if (!$confirmedAt) {
            return __('Not confirmed');
        }

        return __('Confirmed');
    }

    /**
     * @param $entity_id
     *
     * @return bool
     */
    protected function getIsConfirmed($entity_id)
    {
        if (!array_key_exists($entity_id, $this->models)) {
            return false;
        }

        /** @var PostNLShipment $model */
        $model = $this->models[$entity_id];
        $confirmedAt = $model->getConfirmedAt();

        if ($confirmedAt === null) {
            return false;
        }

        return true;
    }
}
