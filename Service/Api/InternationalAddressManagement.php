<?php

namespace TIG\PostNL\Service;

use TIG\PostNL\Api\InternationalAddressManagementInterface;
use TIG\PostNL\Logging\Log;


class InternationalAddressManagement implements InternationalAddressManagementInterface
{
    /** @var Log */
    private $logger;

    /**
     * @param Log                 $logger
     */
    public function __construct(

        Log $logger
    ) {
        $this->logger = $logger;
    }




}
