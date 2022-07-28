<?php
namespace TIG\PostNL\Ui\DataProvider\Matrixgrid\Index;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

class Collection extends SearchResult
{

    protected function _initSelect()
    {
        $this->addFilterToMap('entity_id', 'tig_postnl_matrixrate.entity_id');
        $this->addFilterToMap('destiny_country_id', 'tig_postnl_matrixrate.destiny_country_id');
        parent::_initSelect();
    }
}
