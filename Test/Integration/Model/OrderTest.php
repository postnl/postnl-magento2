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
        $model->setCreatedAt($newTime);
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
