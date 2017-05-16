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
namespace TIG\PostNL\Api\Data;

// @codingStandardsIgnoreFile
/**
 * Too many public methods for the code inspection.
 */
interface OrderInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setOrderId($value);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setQuoteId($value);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setDeliveryDate($value);

    /**
     * @return string
     */
    public function getDeliveryDate();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeStart($value);

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeStart();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeEnd($value);

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeEnd();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setIsPakjegemak($value);

    /**
     * @return bool
     */
    public function getIsPakjegemak();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgOrderAddressId($value);

    /**
     * @return int
     */
    public function getPgOrderAddressId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgLocationCode($value);

    /**
     * @return string
     */
    public function getPgLocationCode();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgRetailNetworkId($value);

    /**
     * @return string
     */
    public function getPgRetailNetworkId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setProductCode($value);

    /**
     * @return int
     */
    public function getProductCode();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setFee($value);

    /**
     * @return float
     */
    public function getFee();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setShipAt($value);

    /**
     * @return string
     */
    public function getShipAt();

    /**
     * @return mixed
     */
    public function getParcelCount();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setParcelCount($value);

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setConfirmedAt($value);

    /**
     * @return string
     */
    public function getConfirmedAt();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setUpdatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();
}
