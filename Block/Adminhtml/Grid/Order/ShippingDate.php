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
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
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
     * @var DateTimeFormatterInterface
     */
    private $dateTimeFormatterInterface;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        TimezoneInterface $timezoneInterface,
        DateTimeFormatterInterface $dateTimeFormatterInterface,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

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
        $shipAt = $this->getShipAt($item);
        if ($shipAt === null) {
            return null;
        }

        return $this->formatShippingDate($shipAt);
    }

    /**
     * @return null|string
     */
    private function getShipAt($item)
    {
        $shipAt = $item['tig_postnl_ship_at'];
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
            return __('Tomorrow');
        }

        if (!$difference->invert) {
            return __('In %1 days', [$days]);
        }

        return $whenToShip->format('d M. Y');
    }
}
