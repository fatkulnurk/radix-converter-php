<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\CustomConverterRegistry;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\HexStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CustomConverterRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear registry before each test to ensure isolation
        CustomConverterRegistry::clear();
    }

    #[Test]
    public function it_registers_converter(): void
    {
        CustomConverterRegistry::register('hex', new HexStrategy());

        $this->assertTrue(CustomConverterRegistry::has('hex'));
    }

    #[Test]
    public function it_gets_registered_converter(): void
    {
        $converter = new HexStrategy();
        CustomConverterRegistry::register('hex', $converter);

        $retrieved = CustomConverterRegistry::get('hex');

        $this->assertSame($converter, $retrieved);
    }

    #[Test]
    public function it_throws_exception_when_registering_duplicate(): void
    {
        CustomConverterRegistry::register('hex', new HexStrategy());

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage("Converter 'hex' is already registered");

        CustomConverterRegistry::register('hex', new HexStrategy());
    }

    #[Test]
    public function it_throws_exception_when_getting_unregistered_converter(): void
    {
        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage("Converter 'hex' is not registered");

        CustomConverterRegistry::get('hex');
    }

    #[Test]
    public function it_checks_if_converter_exists(): void
    {
        $this->assertFalse(CustomConverterRegistry::has('hex'));

        CustomConverterRegistry::register('hex', new HexStrategy());

        $this->assertTrue(CustomConverterRegistry::has('hex'));
    }

    #[Test]
    public function it_unregisters_converter(): void
    {
        CustomConverterRegistry::register('hex', new HexStrategy());

        $result = CustomConverterRegistry::unregister('hex');

        $this->assertTrue($result);
        $this->assertFalse(CustomConverterRegistry::has('hex'));
    }

    #[Test]
    public function it_returns_false_when_unregistering_nonexistent(): void
    {
        $result = CustomConverterRegistry::unregister('nonexistent');

        $this->assertFalse($result);
    }

    #[Test]
    public function it_gets_registered_names(): void
    {
        $binaryClass = new readonly class extends \Fatkulnurk\RadixConverter\Strategies\AbstractBaseConverter {
            private const string CHARSET = '01';
            protected function getCharset(): string { return self::CHARSET; }
        };

        CustomConverterRegistry::register('hex', new HexStrategy());
        CustomConverterRegistry::register('binary', $binaryClass);

        $names = CustomConverterRegistry::getRegisteredNames();

        $this->assertCount(2, $names);
        $this->assertContains('hex', $names);
        $this->assertContains('binary', $names);
    }

    #[Test]
    public function it_clears_all_converters(): void
    {
        CustomConverterRegistry::register('hex', new HexStrategy());
        CustomConverterRegistry::register('binary', new HexStrategy());

        CustomConverterRegistry::clear();

        $this->assertEmpty(CustomConverterRegistry::getRegisteredNames());
    }

    #[Test]
    public function it_gets_all_converters(): void
    {
        $hex = new HexStrategy();
        CustomConverterRegistry::register('hex', $hex);

        $all = CustomConverterRegistry::getAll();

        $this->assertArrayHasKey('hex', $all);
        $this->assertSame($hex, $all['hex']);
    }
}
