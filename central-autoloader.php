<?php
defined('ABSPATH') || exit;

$loader = null;
foreach ([WP_CONTENT_DIR . '/vendor/autoload.php', ABSPATH . 'vendor/autoload.php'] as $file) {
    if (is_readable($file)) {
        $loader = require_once $file;
        break;
    }
}

// Zentrale InstalledVersions VOR allen Plugin-Autoloadern hart laden
if (!class_exists('\Composer\InstalledVersions', false)) {
    foreach ([ WP_CONTENT_DIR . '/vendor/composer/InstalledVersions.php',
               ABSPATH . 'vendor/composer/InstalledVersions.php' ] as $iv) {
        if (is_readable($iv)) { require_once $iv; break; }
    }
}

// Initialzugriff triggert interne Registrierung aller Loader
if (class_exists('\Composer\InstalledVersions', false)) {
    // keine Wirkung nach außen, aber stellt sicher, dass Composer intern initialisiert ist
    \Composer\InstalledVersions::getAllRawData();
}

if ($loader) {
    define('PALASTHOTEL_COMPOSER_CENTRAL', true);
    do_action('palasthotel/central_autoloader_loaded', $loader);
}
