# WordPress Container Plugin

`pixelplugin/wp-container` is a WordPress plugin that provides a global PSR-compatible
dependency container accordingly to [WordPress Container API](https://github.com/pixelpluginhq/wp-container-api). 

## Installation

```shell
composer require pixelplugin/wp-container
```

https://packagist.org/packages/pixelplugin/wp-container

## Usage

1. Install this plugin
2. Require [pixelplugin/wp-container-api](https://github.com/pixelpluginhq/wp-container-api) via composer in your project
3. Now you can use the Container API

## Filters

### wp_container

```php
/**
 * @param array $definitions container definitions.
 */
apply_filters('wp_container', $definitions);
```

The filter should be used to configure container definitions for your code. It supports the following formats:

- `ClassName::class` to simply add some class to the container, but normally it's not needed, because the container supports auto-wiring.
- `InterfaceName::class => ClassName::class` to specify another implementation of some class/interface.
- `ClassName::class => $instance` to bind a class name with a pre-created instance.
- `ClassName::class => $callable` to bind a class name with some callable factory.

This filter is used on `after_setup_theme` action to create the container,
so the container is completely available on `init` hook
(or on the same `after_setup_theme` but with lower priority).
