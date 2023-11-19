<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer;

use League\Container\Container;
use League\Container\ReflectionContainer;

use function add_action;
use function add_management_page;
use function apply_filters;
use function delete_option;
use function esc_html;
use function is_numeric;
use function register_deactivation_hook;
use function sprintf;
use function update_option;

/**
 * WordPress Container Plugin.
 * @link https://github.com/pixelpluginhq/wp-container
 * @link https://packagist.org/packages/pixelplugin/wp-container
 */
final class Plugin
{
    // @phpcs:ignore PSR12.Properties.ConstantVisibility.NotFound
    const OPTION_DEFINITIONS = 'wp_container_definitions';
    // @phpcs:ignore PSR12.Properties.ConstantVisibility.NotFound
    const TITLE = 'WP Container';

    /** @var string */
    private $pluginFile;

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    /**
     * @return void
     */
    public function run()
    {
        add_action('after_setup_theme', [$this, 'afterSetupTheme'], 10, 0);
        add_action('admin_menu', [$this, 'adminMenu'], 10, 0);

        register_deactivation_hook($this->pluginFile, [$this, 'deactivationHook']);
    }

    /**
     * @return void
     */
    public function afterSetupTheme()
    {
        global $wp_container;

        $wp_container = new Container();

        $wp_container->delegate(
            (new ReflectionContainer())->cacheResolutions()
        );

        /**
         * Collect all definitions for the container from others.
         *
         * @since 1.0.0
         *
         * @param array $definitions container definitions.
         */
        $definitions = apply_filters('wp_container', []);
        $defs = [];

        foreach ($definitions as $id => $value) {
            if (is_numeric($id)) {
                $wp_container->add($value);
                $defs[] = [$value, $value];
            } else {
                $wp_container->add($id, $value);
                $defs[] = [$id, is_callable($value) ? 'callable' : $value];
            }
        }

        update_option(self::OPTION_DEFINITIONS, $defs, false);
    }

    /**
     * @return void
     */
    public function adminMenu()
    {
        add_management_page(self::TITLE, self::TITLE, 'manage_options', 'wp-container', [$this, 'managementPage']);
    }

    /**
     * @return void
     */
    public function managementPage()
    {
        echo sprintf(
            '<div class="wrap"><h1>%s</h1>',
            esc_html(self::TITLE)
        );

        $table = new DefinitionsTable();

        $table->prepare_items();
        $table->display();

        echo '</div>';
    }

    /**
     * @return void
     */
    public function deactivationHook()
    {
        delete_option(self::OPTION_DEFINITIONS);
    }
}
