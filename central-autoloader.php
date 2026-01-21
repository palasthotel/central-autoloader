<?php
defined('ABSPATH') || exit;

$loader = null;
foreach ([WP_CONTENT_DIR . '/vendor/autoload.php', ABSPATH . 'vendor/autoload.php'] as $file) {
    if (is_readable($file)) {
        $loader = require_once $file;
        break;
    }
}

if ($loader) {
    define('PALASTHOTEL_COMPOSER_CENTRAL', true);
    do_action('palasthotel/central_autoloader_loaded', $loader);
}
