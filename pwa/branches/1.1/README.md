# Pollen Solutions Embed Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://svn.tigreblanc.fr/pollen-solutions/pwa/tags/1.0.0)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.3-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

**Embed** Component.

## Installation

```bash
composer require pollen-solutions/pwa
```

## Basic Usage

## Setup

### Wordpress Usage (recommended)

```php
use Pollen\Pwa\Pwa;

add_action('after_setup_theme', function () {
    $pwa = new Pwa();
});
```

## Pollen Framework Setup

### Declaration

```php
// config/app.php
use Pollen\Pwa\PwaServiceProvider;

return [
      //...
      'providers' => [
          //...
          PwaServiceProvider::class,
          //...
      ]
      // ...
];
```

### Configuration

```php
// config/pwa.php
// @see /vendor/pollen-solutions/pwa/resources/config/pwa.php.stub
return [

];
```

### Package.json setup

package.json

```json
{
  "dependencies": {
    "pollen-pwa": "file:./vendor/pollen-solutions/pwa"
  }
}
```