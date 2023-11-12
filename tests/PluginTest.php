<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer\Tests;

use PixelPlugin\WPContainer\Plugin;
use PixelPlugin\WPContainer\Tests\Examples\ClassWithInterface;
use PixelPlugin\WPContainer\Tests\Examples\SomeClass;
use PixelPlugin\WPContainer\Tests\Examples\OneMoreClass;
use PixelPlugin\WPContainer\Tests\Examples\ClassWithParameters;
use PixelPlugin\WPContainer\Tests\Examples\SomeInterface;
use Psr\Container\ContainerInterface;
use stdClass;
use WP_Mock;
use WP_Mock\Functions;
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

        $this->plugin = new Plugin(dirname(__DIR__) . '/wp-container.php');
    }

    /**
     * @return void
     */
    public function testInitialization()
    {
        WP_Mock::userFunction('register_deactivation_hook', [
            'times' => 1,
            'args' => [Functions::type('string'), Functions::type('array')]
        ]);

        WP_Mock::expectActionAdded('after_setup_theme', [Functions::type(Plugin::class), 'afterSetupTheme'], 10, 0);
        WP_Mock::expectActionAdded('admin_menu', [Functions::type(Plugin::class), 'adminMenu'], 10, 0);

        require dirname(__DIR__) . '/wp-container.php';

        $this->assertConditionsMet();
    }

    /**
     * @return void
     * @covers ::run
     */
    public function testRun()
    {
        WP_Mock::userFunction('register_deactivation_hook', [
            'times' => 1,
            'args' => [Functions::type('string'), [$this->plugin, 'deactivationHook']]
        ]);

        WP_Mock::expectActionAdded('after_setup_theme', [$this->plugin, 'afterSetupTheme'], 10, 0);
        WP_Mock::expectActionAdded('admin_menu', [$this->plugin, 'adminMenu'], 10, 0);

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

        WP_Mock::userFunction('update_option', [
            'times' => 1,
            'args' => [Plugin::OPTION_DEFINITIONS, [], false]
        ]);

        WP_Mock::expectFilter('wp_container', []);

        $this->plugin->afterSetupTheme();

        $this->assertConditionsMet();
        $this->assertInstanceOf(ContainerInterface::class, $wp_container);
    }

    /**
     * @return void
     * @covers ::afterSetupTheme
     */
    public function testDefinitions()
    {
        /** @var ContainerInterface $wp_container */
        global $wp_container;

        WP_Mock::userFunction('update_option', [
            'times' => 1,
            'args' => [Plugin::OPTION_DEFINITIONS, Functions::type('array'), false]
        ]);

        $someClassInstance = new SomeClass();

        WP_Mock::onFilter('wp_container')
            ->with([])
            ->reply([
                SomeInterface::class => ClassWithInterface::class,
                SomeClass::class => $someClassInstance,
                OneMoreClass::class,
                ClassWithParameters::class => function () {
                    return new ClassWithParameters(42);
                }
            ]);

        $this->plugin->afterSetupTheme();

        $this->assertConditionsMet();

        $this->assertTrue($wp_container->has(SomeInterface::class));
        $this->assertTrue($wp_container->has(SomeClass::class));
        $this->assertTrue($wp_container->has(OneMoreClass::class));
        $this->assertTrue($wp_container->has(ClassWithParameters::class));

        $this->assertInstanceOf(ClassWithInterface::class, $wp_container->get(SomeInterface::class));
        $this->assertSame($someClassInstance, $wp_container->get(SomeClass::class));
        $this->assertInstanceOf(OneMoreClass::class, $wp_container->get(OneMoreClass::class));
        $this->assertInstanceOf(ClassWithParameters::class, $wp_container->get(ClassWithParameters::class));
    }

    /**
     * @return void
     */
    public function testAutoWiring()
    {
        /** @var ContainerInterface $wp_container */
        global $wp_container;

        WP_Mock::userFunction('update_option', [
            'times' => 1,
            'args' => [Plugin::OPTION_DEFINITIONS, [], false]
        ]);

        WP_Mock::expectFilter('wp_container', []);

        $this->plugin->afterSetupTheme();

        $this->assertConditionsMet();
        $this->assertInstanceOf(stdClass::class, $wp_container->get(stdClass::class));
    }
}
