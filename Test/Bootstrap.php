<?php

if (function_exists('setCustomErrorHandler')) {
    return;
}

/**
 * From vendor/tig/postnl
 */
$path = __DIR__ . '/../../../../dev/tests/unit/framework/bootstrap.php';

if (strpos(__DIR__, 'app/code') !== false) {
    /**
     * From app/code/TIG/PostNL
     */
    $path = __DIR__ . '/../../../../../dev/tests/unit/framework/bootstrap.php';
}

require_once($path);
