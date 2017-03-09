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

interface ShipmentBarcodeRepositoryInterface
{
    /**
     * Save a PostNL Shipment Barcode.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function save(\TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode);

    /**
     * Create a PostNL Shipment Barcode.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function create();

    /**
     * Return a specific PostNL Shipment Barcode.
     *
     * @api
     * @param int $identifier
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function getById($identifier);

    /**
     * Retrieve a list of PostNL Shipment Barcodes.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific PostNL Shipment Barcode.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\ShipmentBarcodeInterface $shipmentBarcode);
}
