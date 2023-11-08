<?php

/**
 * WordPress Container Plugin.
 *
 * Plugin Name: WP Container
 * Description: WP Container is a global PSR-compatible dependency container for WordPress applications.
 * Version: 1.0.0
 * Plugin URI: https://pixelplugin.com
 * Author: PixelPlugin
 * Author URI: https://pixelplugin.com
 * License: MIT
 * Requires PHP: 7.0
 *
 * @wordpress-plugin
 */

use PixelPlugin\WPContainer\Plugin;

if (!function_exists('add_filter')) {
    http_response_code(403);
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

(new Plugin())->run();
