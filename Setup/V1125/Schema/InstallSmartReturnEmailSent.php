<?php

namespace TIG\PostNL\Setup\V1125\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallSmartReturnEmailSent extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';


    // @codingStandardsIgnoreLine
    protected $columns = [
        'smart_return_email_sent'
    ];

    /**
     * @return array
     */
    public function installSmartReturnEmailSentColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default'  => 0,
            'comment'  => 'Smart Return Email Sent',
            'after'    => 'smart_return_barcode',
        ];
    }
}
