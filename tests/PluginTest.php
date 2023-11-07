<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer\Tests;

use League\Container\Container;
use PixelPlugin\WPContainer\Plugin;
use WP_Mock;
use WP_Mock\Tools\TestCase;

/**
 * Test the whole functionality from `wp-container.php` and `Plugin.php`.
 * @coversDefaultClass \PixelPlugin\WPContainer\Plugin
 */
final class PluginTest extends TestCase
{
    /** @var Plugin */
    private $plugin;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->plugin = new Plugin();
    }

    /**
     * @return void
     * @covers ::run
     */
    public function testRun()
    {
        WP_Mock::expectActionAdded('after_setup_theme', [$this->plugin, 'afterSetupTheme'], 10, 0);

        $this->plugin->run();

        $this->assertConditionsMet();
    }

    /**
     * @return void
     * @covers ::afterSetupTheme
     */
    public function testAfterSetupTheme()
    {
        global $wp_container;

        $this->assertNull($wp_container);

        WP_Mock::expectFilter('wp_container', []);

        $this->plugin->afterSetupTheme();

        $this->assertConditionsMet();
        $this->assertInstanceOf(Container::class, $wp_container);
    }
}
