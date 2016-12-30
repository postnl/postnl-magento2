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

use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Model\ShipmentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

class ShippingDate extends AbstractGrid
{
    /**
     * @var TimezoneInterface
     */
    private $timezoneInterface;

    /**
     * @var PostNLShipment
     */
    private $model = null;

    /**
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatterInterface;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShipmentFactory $shipmentFactory,
        TimezoneInterface $timezoneInterface,
        DateTimeFormatterInterface $dateTimeFormatterInterface,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $shipmentFactory, $components, $data);

        $this->timezoneInterface = $timezoneInterface;
        $this->dateTimeFormatterInterface = $dateTimeFormatterInterface;
    }

    /**
     * @param $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $entity_id = $item['entity_id'];

        if (!$this->loadModel($entity_id)) {
            return null;
        }

        $shipAt = $this->getShipAt();
        if ($shipAt === null) {
            return null;
        }

        return $this->formatShippingDate($shipAt);
    }

    /**
     * @param $entity_id
     *
     * @return bool|PostNLShipment
     */
    private function loadModel($entity_id)
    {
        if (!array_key_exists($entity_id, $this->models)) {
            return false;
        }

        $this->model = $this->models[$entity_id];
        return true;
    }

    /**
     * @return null|string
     */
    private function getShipAt()
    {
        $shipAt = $this->model->getShipAt();
        if ($shipAt === null) {
            return null;
        }

        return $shipAt;
    }

    /**
     * @param $shipAt
     *
     * @return null|int
     */
    private function formatShippingDate($shipAt)
    {
        $now = $this->timezoneInterface->date();
        $whenToShip = $this->timezoneInterface->date($shipAt);
        $difference = $now->diff($whenToShip);
        $days = $difference->days;

        if ($days == 0) {
            return __('Today');
        }

        if (!$difference->invert && $days === 1) {
            return __('In 1 day');
        }

        if (!$difference->invert) {
            return __('In %1 days', [$days]);
        }

        return $whenToShip->format('d M. Y');
    }
}
