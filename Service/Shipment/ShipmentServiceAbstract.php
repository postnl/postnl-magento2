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
namespace TIG\PostNL\Service\Shipment;

use \TIG\PostNL\Logging\Log;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;

abstract class ShipmentServiceAbstract
{
    /**
     * @var Log
     */
    //@codingStandardsIgnoreLine
    protected $logger;

    /**
     * @var ShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $shipmentRepository;

    /**
     * @var PostNLShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $postnlShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    //@codingStandardsIgnoreLine
    protected $searchCriteriaBuilder;

    /**
     * @param Log                      $log
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param ShipmentRepository       $shipmentRepository
     */
    public function __construct(
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $log;
        $this->postnlShipmentRepository = $postNLShipmentRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $identifier
     *
     * @return PostNLShipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostNLShipment($identifier)
    {
        return $this->postnlShipmentRepository->getById($identifier);
    }

    /**
     * @param $identifier
     *
     * @return Shipment
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShipment($identifier)
    {
        return $this->shipmentRepository->get($identifier);
    }
}
