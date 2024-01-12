<?php

namespace TIG\PostNL\Service\Shipment\Label\Merge;

use TIG\PostNL\Service\Pdf\Fpdi;

interface MergeInterface
{
    /**
     * @param Fpdi[] $labels
     * @param bool   $createNewPdf
     *
     * @return Fpdi
     */
    public function files(array $labels, $createNewPdf = false);
}
