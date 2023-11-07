<?php

declare(strict_types=1);

namespace PixelPlugin\WPContainer\Tests\Examples;

/**
 * An example of a class that requires a constructor parameter
 * that cannot be normally set by the container without extra configuration.
 */
final class ClassWithParameters
{
    public function __construct(int $value)
    {
    }
}
