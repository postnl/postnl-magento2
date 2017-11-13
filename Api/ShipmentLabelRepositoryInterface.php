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
namespace TIG\PostNL\Api;

interface ShipmentLabelRepositoryInterface
{
    /**
     * Save a PostNL Shipment Label.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function save(\TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel);

    /**
     * Create a PostNL Shipment Label.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function create();

    /**
     * Return a specific PostNL Shipment Label.
     *
     * @api
     * @param int $identifier
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function getById($identifier);

    /**
     * Return a label that belongs to a shipment.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function getByShipment(\TIG\PostNL\Api\Data\ShipmentInterface $shipment);

    /**
     * Retrieve a list of PostNL Shipment Labels.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific PostNL Shipment Label.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\ShipmentLabelInterface $shipmentLabel);
}
