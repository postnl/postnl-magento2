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
interface MatrixrateInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWebsiteId($value);

    /**
     * @return string
     */
    public function getDestinyCountryId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyCountryId($value);

    /**
     * @return int
     */
    public function getDestinyRegionId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyRegionId($value);

    /**
     * @return string
     */
    public function getDestinyZipCode();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyZipCode($value);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWeight($value);

    /**
     * @return float
     */
    public function getSubtotal();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setSubtotal($value);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setQuantity($value);

    /**
     * @return string
     */
    public function getParcelType();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setParcelType($value);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setPrice($value);
}
