## Setup

WordPress MU-plugin that loads a central Composer autoloader.
Enables multiple plugins to share dependencies safely without duplicate `vendor/` directories.

The following is an example setup using the **pro-litteris** plugin.

---

### 1. Create a root `composer.json`

Create a `composer.json` in your project root.

> **Note:**  
> If you install plugins from Git repositories (VCS), you must add them to the
> `repositories` section. Plugins available on Packagist do not require this.

Example `composer.json`:

```php
{
  "name": "palasthotel/zentralplus",
  "type": "project",
  "config": {
    "vendor-dir": "docroot/wp-content/vendor",
    "optimize-autoloader": true,
    "sort-packages": true,
    "preferred-install": {
      "*": "dist",
      "palasthotel/*": "dist"
    },
    "allow-plugins": {
      "composer/installers": true
    }
  },
  "extra": {
    "installer-paths": {
      "docroot/wp-content/plugins/{$name}/": ["type:wordpress-plugin"],
      "docroot/wp-content/mu-plugins/{$name}/": ["type:wordpress-muplugin"]
    }
  },
  "repositories": [
    { "type": "composer", "url": "https://repo.packagist.org" },
    { "type": "composer", "url": "https://wpackagist.org" },
    { "type": "vcs", "url": "https://github.com/palasthotel/central-autoloader.git" },
    { "type": "vcs", "url": "https://github.com/palasthotel/pro-litteris.git" }
  ],
  "require": {}
}
```

### 2. Require central autoloader && MU-plugin:

`composer require composer/installers`

`composer require palasthotel/central-autoloader`
(Das geht gerade noch nicht. Aktuell muss die central-autoloader.php händisch in den mu-plugins Ordner gelegt werden)

### 3. Require plugins that got defined as repositories

`composer require palasthotel/pro-litteris`

---

## Setup for Plugins that should get loaded with the central autoloader

### Put this at the beginning of the main plugin.php

```json
// composer package name is defined in plugins composer.json
const COMPOSER_PACKAGE = 'palasthotel/pro-litteris';

$centralAutoloader = (defined('PALASTHOTEL_COMPOSER_CENTRAL') && constant('PALASTHOTEL_COMPOSER_CENTRAL'))
    || did_action('palasthotel/central_autoloader_loaded') > 0;

$managedByCentralAutoloader = false;
if ($centralAutoloader && class_exists('\Composer\InstalledVersions', true)) { //checks if autoloader exists
    try {
        if (\Composer\InstalledVersions::isInstalled(COMPOSER_PACKAGE)) { // this only checks for some version not the directory 
            $installPath = \Composer\InstalledVersions::getInstallPath(COMPOSER_PACKAGE);
            $managedByCentralAutoloader = $installPath && realpath($installPath) && realpath($installPath) === realpath(__DIR__); // check if the it is acutally THIS version and dir installed
        }
    } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[ProLitteris] InstalledVersions exception: ' . $e->getMessage());
        }
    }
}

if (!$centralAutoloader || !$managedByCentralAutoloader) {
    $local = __DIR__ . '/vendor/autoload.php';
    if (is_readable($local)) {
        require_once $local;
    } else {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>Bitte "composer install" im ' . COMPOSER_PACKAGE .  ' Plugin-Ordner ausführen.</p></div>';
        });
        return;
    }
}
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('[ProLitteris] centralAutoloader=' . ($centralAutoloader ? '1' : '0')
        . ' classExists=' . (class_exists('\Composer\InstalledVersions', false) ? '1' : '0')
        . ' installPath=' . ($installPath ?? '(none)')
        . ' managed=' . ($managedByCentralAutoloader ? '1' : '0'));
}
```
