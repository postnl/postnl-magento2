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

namespace TIG\PostNL\Service\Carrier;

use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Options\ItemsToOption;

class ParcelTypeFinder
{
    const DEFAULT_TYPE = 'regular';

    /**
     * @var ItemsToOption
     */
    private $itemsToOption;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * ParcelTypeFinder constructor.
     *
     * @param ItemsToOption            $itemsToOption
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ItemsToOption $itemsToOption,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->itemsToOption = $itemsToOption;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return string
     */
    public function get()
    {
        $result = $this->itemsToOption->getFromQuote();
        if ($result) {
            return $result;
        }

        $order = $this->orderRepository->getByQuoteId();

        if ($order === null) {
            return static::DEFAULT_TYPE;
        }

        return $this->parseDatabaseValue($order->getType());
    }

    /**
     * @param $type
     *
     * @return string
     */
    private function parseDatabaseValue($type)
    {
        switch ($type) {
            case 'PG':
                return 'pakjegemak';
        }

        return static::DEFAULT_TYPE;
    }
}
