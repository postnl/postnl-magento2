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
namespace TIG\PostNL\Service\Shipment\Barcode;

use TIG\PostNL\Model\ShipmentBarcodeRepository;
use TIG\PostNL\Api\Data\ShipmentBarcodeInterface;
use TIG\PostNL\Service\Shipment\ShipmentServiceAbstract;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

class DeleteBarcode extends ShipmentServiceAbstract
{
    /**
     * @var ShipmentBarcodeRepository
     */
    private $shipmentBarcodeRepository;

    /**
     * @param ShipmentBarcodeRepository $shipmentBarcodeRepository
     * @param Log                       $log
     * @param PostNLShipmentRepository  $postNLShipmentRepository
     * @param ShipmentRepository        $shipmentRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentBarcodeRepository $shipmentBarcodeRepository,
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $log,
            $postNLShipmentRepository,
            $shipmentRepository,
            $searchCriteriaBuilder
        );

        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
    }

    /**
     * Deletes a single barcode.
     *
     * @param ShipmentBarcodeInterface $barcode
     */
    public function delete($barcode)
    {
        try {
            $this->shipmentBarcodeRepository->delete($barcode);
        } catch (PostNLException $exception) {
            $this->logger->alert('Can\'t delete shipment barcode', $exception->getLogMessage());
        }
    }

    /**
     * Deletes all barcodes associated to the PostNL Shipment ID.
     *
     * @param $postNLShipmentId
     */
    public function deleteAllByShipmentId($postNLShipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'parent_id',
            $postNLShipmentId
        );

        $barcodes = $this->shipmentBarcodeRepository->getList($searchCriteria->create());

        /** @var ShipmentBarcodeInterface $barcode */
        foreach ($barcodes->getItems() as $barcode) {
            // @codingStandardsIgnoreLine
            $this->delete($barcode);
        }
    }
}
