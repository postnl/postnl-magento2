<?php

namespace TIG\PostNL\Test\Integration\Model;

use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Test\Integration\TestCase;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\SentDate;

class OrderRepositoryTest extends TestCase
{
    public function testThatAnEmptyFilterDoesNotThrowAnException()
    {
        $this->disableEndpoint(DeliveryDate::class);
        $this->disableEndpoint(SentDate::class);

        /** @var OrderRepository $repository */
        $repository = $this->getObject(OrderRepository::class);

        $model = $this->getNewModel();
        $model->setDataChanges(true);
        $repository->save($model);
        $savedModel = $repository->getByFieldWithValue('entity_id', $model->getEntityId());

        /** @var SearchCriteriaBuilder $criteriaBuilder */
        $criteriaBuilder = $this->getObject(SearchCriteriaBuilder::class);
        $criteriaBuilder->addFilter('order_id', [], 'IN');
        $criteriaBuilder->addFilter('entity_id', [$savedModel->getEntityId()], 'IN');
        $criteria = $criteriaBuilder->create();

        $result = $repository->getList($criteria);
        $items  = $result->getItems();
        $item   = end($items);

        $this->assertCount(1, $items);
        $this->assertEquals($savedModel, $item);
    }

    /**
     * @return Order|OrderFactory
     */
    private function getNewModel()
    {
        /** @var OrderFactory $model */
        $factory = $this->getObject(OrderFactory::class);

        /** @var Order $model */
        $model = $factory->create();

        return $model;
    }
}
