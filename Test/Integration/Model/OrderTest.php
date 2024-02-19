<?php

namespace TIG\PostNL\Test\Integration\Model;

use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Test\Integration\TestCase;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

class OrderTest extends TestCase
{
    public function theFeeIsSavedCorrectlyProvider()
    {
        return [
            ['2.5', 2.5],
            ['2.0', 2],
        ];
    }

    /**
     * @dataProvider theFeeIsSavedCorrectlyProvider
     *
     * @param $input
     * @param $output
     *
     * @throws \Exception
     */
    public function testTheFeeIsSavedCorrectly($input, $output)
    {
        $this->disableEndpoint(DeliveryDate::class);
        $this->disableEndpoint(SentDate::class);

        $model = $this->getNewModel();
        $model->setFee($input);
        $repository = $this->saveModel($model);

        $newModel = $repository->getById($model->getId());

        $this->assertEquals($output, $newModel->getFee());
    }

    public function testTheCreatedAtAndUpdatedAtIsSetAtSave()
    {
        $model = $this->getNewModel();
        $model->setDataChanges(true);
        $this->saveModel($model);

        $this->assertNotNull($model->getCreatedAt());
        $this->assertNotNull($model->getUpdatedAt());

        /**
         * The updated at should change when the model is saved again.
         * We use a date in the past as this test is run within a second and so the date is not changed.
         */
        $newTime = date('Y-m-d H:i:s', strtotime('last week thursday'));
        $model->changeCreatedAt($newTime);
        $this->saveModel($model);
        $this->assertNotEquals($newTime, $model->getUpdatedAt());
    }

    /**
     * @return Order
     */
    private function getNewModel()
    {
        /** @var OrderFactory $factory */
        $factory = $this->getObject(OrderFactory::class);

        /** @var Order $model */
        return $factory->create();
    }

    /**
     * @param $model
     *
     * @return OrderRepository
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveModel(Order $model)
    {
        /** @var OrderRepository $repository */
        $repository = $this->getObject(OrderRepository::class);
        $repository->save($model);

        return $repository;
    }
}
