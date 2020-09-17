<?php
/**
 * *
 *  *
 *  *          ..::..
 *  *     ..::::::::::::..
 *  *   ::'''''':''::'''''::
 *  *   ::..  ..:  :  ....::
 *  *   ::::  :::  :  :   ::
 *  *   ::::  :::  :  ''' ::
 *  *   ::::..:::..::.....::
 *  *     ''::::::::::::''
 *  *          ''::''
 *  *
 *  *
 *  * NOTICE OF LICENSE
 *  *
 *  * This source file is subject to the Creative Commons License.
 *  * It is available through the world-wide-web at this URL:
 *  * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *  * If you are unable to obtain it through the world-wide-web, please send an email
 *  * to servicedesk@tig.nl so we can send you a copy immediately.
 *  *
 *  * DISCLAIMER
 *  *
 *  * Do not edit or add to this file if you wish to upgrade this module to newer
 *  * versions in the future. If you wish to customize this module for your
 *  * needs please contact servicedesk@tig.nl for more information.
 *  *
 *  * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 *  * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 *
 */
namespace TIG\PostNL\Model;

trait OrderRepositoryTrait
{
    /**
     * Retrieve the most recent order record for a quote
     *
     * @param $quoteId
     *
     * @return mixed|null
     * @throws \Magento\Framework\Exception\InputException
     */
    public function retrieveCurrentPostNLOrder($quoteId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('quote_id', $quoteId);

        // Multiple records might exist, retrieve the most recent
        $sortOrder = $this->sortOrderBuilder->create();
        $sortOrder->setField('entity_id');
        $sortOrder->setDirection('DESC');

        $searchCriteria->setSortOrders([$sortOrder]);
        $searchCriteria->setPageSize(1);

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->getList($searchCriteria->create());

        if ($list->getTotalCount()) {
            return array_values($list->getItems())[0];
        }

        return null;
    }
}
