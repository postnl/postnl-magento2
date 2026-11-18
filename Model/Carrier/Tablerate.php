<?php

namespace TIG\PostNL\Model\Carrier;

use Magento\OfflineShipping\Model\Carrier\Tablerate as MagentoTablerate;

class Tablerate extends MagentoTablerate
{
    /** @var string */
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

}
