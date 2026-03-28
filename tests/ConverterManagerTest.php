<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\ConverterManager;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\HexStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConverterManagerTest extends TestCase
{
    #[Test]
    public function it_creates_manager_without_custom_converters(): void
    {
        $manager = new ConverterManager();

        $this->assertInstanceOf(ConverterManager::class, $manager);
    }

    #[Test]
    public function it_creates_manager_with_custom_converters(): void
    {
        $manager = new ConverterManager([
            'hex' => new HexStrategy(),
        ]);

        $this->assertTrue($manager->hasCustom('hex'));
    }

    #[Test]
    public function it_gets_converter_instance(): void
    {
        $manager = new ConverterManager();

        $converter = $manager->get(ConverterType::BASE62);

        $this->assertInstanceOf(\Fatkulnurk\RadixConverter\Strategies\Base62Strategy::class, $converter);
    }

    #[Test]
    public function it_returns_same_instance_on_multiple_gets(): void
    {
        $manager = new ConverterManager();

        $converter1 = $manager->get(ConverterType::BASE62);
        $converter2 = $manager->get(ConverterType::BASE62);

        $this->assertSame($converter1, $converter2);
    }

    #[Test]
    public function it_encodes_using_converter_type(): void
    {
        $manager = new ConverterManager();

        $encoded = $manager->encode(ConverterType::BASE62, 12345);

        $this->assertEquals('3d7', $encoded);
    }

    #[Test]
    public function it_decodes_using_converter_type(): void
    {
        $manager = new ConverterManager();

        $decoded = $manager->decode(ConverterType::BASE62, '3d7');

        $this->assertEquals(12345, $decoded);
    }

    #[Test]
    public function it_registers_custom_converter(): void
    {
        $manager = new ConverterManager();

        $manager->registerCustom('hex', new HexStrategy());

        $this->assertTrue($manager->hasCustom('hex'));
    }

    #[Test]
    public function it_throws_exception_when_registering_duplicate(): void
    {
        $manager = new ConverterManager(['hex' => new HexStrategy()]);

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage("Converter 'hex' is already registered");

        $manager->registerCustom('hex', new HexStrategy());
    }

    #[Test]
    public function it_gets_custom_converter_names(): void
    {
        $binaryClass = new readonly class extends \Fatkulnurk\RadixConverter\Strategies\AbstractBaseConverter {
            private const string CHARSET = '01';
            protected function getCharset(): string { return self::CHARSET; }
        };

        $manager = new ConverterManager([
            'hex' => new HexStrategy(),
            'binary' => $binaryClass,
        ]);

        $names = $manager->getCustomNames();

        $this->assertCount(2, $names);
        $this->assertContains('hex', $names);
        $this->assertContains('binary', $names);
    }

    #[Test]
    public function it_clears_cache(): void
    {
        $manager = new ConverterManager();

        // Access converter to cache it
        $manager->get(ConverterType::BASE62);

        $manager->clearCache();

        // After clear, should create new instance
        $converter = $manager->get(ConverterType::BASE62);
        $this->assertInstanceOf(\Fatkulnurk\RadixConverter\Strategies\Base62Strategy::class, $converter);
    }

    #[Test]
    public function it_clears_all_converters(): void
    {
        $manager = new ConverterManager([
            'hex' => new HexStrategy(),
        ]);

        $manager->clearAll();

        $this->assertFalse($manager->hasCustom('hex'));
    }

    #[Test]
    public function it_throws_exception_for_unknown_string_type(): void
    {
        $manager = new ConverterManager();

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Unknown converter type: unknown');

        $manager->get('unknown');
    }
}
