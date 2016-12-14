<?php
namespace TIG\PostNL\Api;

use TIG\PostNL\Model\OrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderRepositoryInterface
{
    public function save(OrderInterface $order);

    public function getById($identifier);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(OrderInterface $order);
}
