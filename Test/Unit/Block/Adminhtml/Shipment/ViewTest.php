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
namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Shipment;

use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \TIG\PostNL\Api\ShipmentRepositoryInterface;
use \Magento\Framework\Api\SearchResults;
use \TIG\PostNL\Block\Adminhtml\Shipment\View;
use TIG\PostNL\Test\TestCase;

class ViewTest extends TestCase
{
    protected $instanceClass = View::class;

    public function testGetPostNLShipment()
    {
        $this->markTestSkipped('Rik : Needs to be finished, working on constructor');

        $instance = $this->getInstance([
            'postNLShipmentRepository' => $this->getShipmentRepository(),
            'searchCriteriaBuilder'    => $this->getSearchCriteriaBuilder()
        ]);

        $result = $this->invoke('getPostNLShipment', $instance);
        $this->assertNotEmpty($result);
    }

    private function getShipmentRepository()
    {
        $shipmentRepository = $this->getFakeMock(ShipmentRepositoryInterface::class)->getMock();

        $getListExpects = $shipmentRepository->expects($this->once());
        $getListExpects->method('getList');
        $getListExpects->with($this->getSearchCriteria());
        $getListExpects->willReturn($this->getShipmentReturned());

        return $shipmentRepository;
    }

    public function getShipmentReturned()
    {
        $searchResults = $this->getMock(SearchResults::class);

        $getTotalCountExpects = $searchResults->expects($this->once());
        $getTotalCountExpects->method('getTotalCount');
        $getTotalCountExpects->willReturn(1);

        $getItemsExpects = $searchResults->expects($this->once());
        $getItemsExpects->method('getItems');
        $getItemsExpects->willReturn(1);

        return $searchResults;
    }

    private function getSearchCriteriaBuilder()
    {
        $builder = $this->getFakeMock(SearchCriteriaBuilder::class)->getMock();

        $filterExpects = $builder->expects($this->once());
        $filterExpects->method('addFilter');
        $filterExpects->with('shipment_id', 13);
        $filterExpects->willReturnSelf();

        $pageSizeExpects = $builder->expects($this->once());
        $pageSizeExpects->method('setPageSize');
        $pageSizeExpects->with(1);
        $pageSizeExpects->willReturnSelf();

        $createExpects = $builder->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturn($this->getSearchCriteria());

        return $builder;
    }

    private function getSearchCriteria()
    {
        return $this->getMock(SearchCriteriaInterface::class);
    }
}