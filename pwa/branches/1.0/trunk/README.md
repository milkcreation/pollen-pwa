# Pollen Solutions Embed Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://svn.tigreblanc.fr/pollen-solutions/pwa/tags/1.0.0)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

**Embed** Component.

## Installation

```bash
composer require pollen-solutions/pwa
```

## Setup

### Framework Declaration

In config/app.php file.

```php
return [
      //...
      'providers' => [
          //...
          \Pollen\Pwa\PwaServiceProvider::class,
          //...
      ];
      // ...
];
```

### Standalone Declaration


### NPM Usage

In package.json file.

```json
{
  "dependencies": {
    "pollen-pwa": "file:./vendor/pollen-solutions/pwa"
  }
}
```


### Configuration

```php
// config/pwa.php
// @see /vendor/pollen-solutions/pwa/resources/config/pwa.php
return [
      //...

      // ...
];
```