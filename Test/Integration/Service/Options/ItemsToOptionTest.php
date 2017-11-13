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
namespace TIG\PostNL\Test\Integration\Service\Options;

use TIG\PostNL\Test\Integration\TestCase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use TIG\PostNL\Service\Options\ItemsToOption;
use TIG\PostNL\Service\Order\ProductCodeAndType;

class ItemsToOptionTest extends TestCase
{
    public $instanceClass = ItemsToOption::class;

    public function testGetWithExtraAtHomeOrder()
    {
        require __DIR__.'/../../../Fixtures/Extra_at_home/ExtraAtHomeOrder.php';

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $this->getOrder();
        $result = $this->getInstance()->get($order->getItems());

        $this->assertEquals(ProductCodeAndType::OPTION_EXTRAATHOME, $result);
    }

    public function testReqularOrder()
    {
        require __DIR__.'/../../../Fixtures/Extra_at_home/NonExtraAtHomeOrder.php';

        /** @var \Magento\Sales\Model\Order $order */
        $order  = $this->getOrder();
        $result = $this->getInstance()->get($order->getItems());

        $this->assertEquals('', $result);
    }

    /**
     * @return \Magento\Framework\DataObject|\Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        /** @var Collection $orderCollection */
        $orderCollection = $this->getObject(Collection::class);
        $orderCollection->addFieldToFilter('customer_email', 'customer@tig.nl');

        return $orderCollection->getFirstItem();
    }
}
