<?php

namespace TIG\PostNL\Service\Shipment\Labelling\Handler;

interface HandlerInterface
{
    /**
     * @param object $labelItems
     *
     * @return string (base64_encode)
     */
    public function format($labelItems);

    /**
     * Cleanup after we are done.
     */
    public function cleanup();
}
