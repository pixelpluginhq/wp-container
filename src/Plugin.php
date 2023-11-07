<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer;

use League\Container\Container;

use function add_action;
use function apply_filters;
use function is_numeric;

final class Plugin
{
    /**
     * @return void
     */
    public function run()
    {
        add_action('after_setup_theme', [$this, 'afterSetupTheme'], 10, 0);
    }

    /**
     * @return void
     */
    public function afterSetupTheme()
    {
        global $wp_container;

        $wp_container = new Container();

        /**
         * Collect all definitions for the container from others.
         *
         * @since 1.0.0
         *
         * @param array $definitions container definitions.
         */
        $definitions = apply_filters('wp_container', []);

        foreach ($definitions as $id => $value) {
            if (is_numeric($id)) {
                $wp_container->add($value);
            } else {
                $wp_container->add($id, $value);
            }
        }
    }
}
