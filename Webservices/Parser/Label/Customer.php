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
namespace TIG\PostNL\Webservices\Parser\Label;

use TIG\PostNL\Webservices\Api\Customer as CustomerApi;

class Customer
{
    /**
     * @var CustomerApi
     */
    private $customer;

    /**
     * @param CustomerApi $customer
     */
    public function __construct(
        CustomerApi $customer
    ) {
        $this->customer = $customer;
    }

    /**
     * @return array
     */
    public function get()
    {
        $customer                       = $this->customer->get();
        $customer['Address']            = $this->customer->address();
        $customer['CollectionLocation'] = $this->customer->blsCode();

        return $customer;
    }

    /**
     * @param $storeId
     */
    public function setStoreId($storeId)
    {
        $this->customer->setStoreId($storeId);
    }
}
