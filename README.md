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

```json
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
    { "type": "vcs", "url": "https://github.com/palasthotel/central-autoloader.git" }
    { "type": "vcs", "url": "https://github.com/palasthotel/pro-litteris.git" }
  ],
  "require": {}
}
```

### 2. Require central autoloader && MU-plugin:

`composer require composer/installers`

`composer require palasthotel/central-autoloader`

### 3. Require plugins that got defined as repositories

`composer require palasthotel/pro-litteris`
