<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\CustomConverterRegistry;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericLowerStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericUpperStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphaOnlyStrategy;
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;
use Fatkulnurk\RadixConverter\Strategies\HexStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConverterFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_base62_converter(): void
    {
        $converter = ConverterFactory::make(ConverterType::BASE62);

        $this->assertInstanceOf(Base62Strategy::class, $converter);
        $this->assertInstanceOf(IDConverterInterface::class, $converter);
    }

    #[Test]
    public function it_creates_alphanumeric_upper_converter(): void
    {
        $converter = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_UPPER);

        $this->assertInstanceOf(AlphanumericUpperStrategy::class, $converter);
    }

    #[Test]
    public function it_creates_alphanumeric_lower_converter(): void
    {
        $converter = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);

        $this->assertInstanceOf(AlphanumericLowerStrategy::class, $converter);
    }

    #[Test]
    public function it_creates_alpha_only_converter(): void
    {
        $converter = ConverterFactory::make(ConverterType::ALPHA_ONLY);

        $this->assertInstanceOf(AlphaOnlyStrategy::class, $converter);
    }

    #[Test]
    public function it_creates_custom_converter_from_registry(): void
    {
        CustomConverterRegistry::register('test_hex', new HexStrategy());

        $converter = ConverterFactory::make('test_hex');

        $this->assertInstanceOf(HexStrategy::class, $converter);

        // Cleanup
        CustomConverterRegistry::unregister('test_hex');
    }

    #[Test]
    public function it_throws_exception_for_unknown_string_type(): void
    {
        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Unknown converter type: unknown_type');

        ConverterFactory::make('unknown_type');
    }

    #[Test]
    public function it_encodes_and_decodes_correctly(): void
    {
        $converter = ConverterFactory::make(ConverterType::BASE62);

        $number = 12345;
        $encoded = $converter->encode($number);
        $decoded = $converter->decode($encoded);

        $this->assertEquals($number, $decoded);
    }
}
