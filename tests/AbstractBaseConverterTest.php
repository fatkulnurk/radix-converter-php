<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\AbstractBaseConverter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AbstractBaseConverterTest extends TestCase
{
    #[Test]
    public function it_implements_converter_interface(): void
    {
        $converter = new TestConcreteConverter();

        $this->assertInstanceOf(IDConverterInterface::class, $converter);
    }

    #[Test]
    public function it_encodes_zero(): void
    {
        $converter = new TestConcreteConverter();

        $result = $converter->encode(0);

        $this->assertEquals('A', $result);
    }

    #[Test]
    public function it_encodes_positive_number(): void
    {
        $converter = new TestConcreteConverter();

        $result = $converter->encode(10);

        $this->assertEquals('K', $result);
    }

    #[Test]
    public function it_encodes_large_number(): void
    {
        $converter = new TestConcreteConverter();

        $result = $converter->encode(1000000);

        $this->assertEquals('CEXHO', $result);
    }

    #[Test]
    public function it_throws_exception_for_negative_number(): void
    {
        $converter = new TestConcreteConverter();

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Input number must be positive');

        $converter->encode(-1);
    }

    #[Test]
    public function it_decodes_string(): void
    {
        $converter = new TestConcreteConverter();

        $result = $converter->decode('K');

        $this->assertEquals(10, $result);
    }

    #[Test]
    public function it_decodes_large_string(): void
    {
        $converter = new TestConcreteConverter();

        $result = $converter->decode('CEXHO');

        $this->assertEquals(1000000, $result);
    }

    #[Test]
    public function it_throws_exception_for_empty_string(): void
    {
        $converter = new TestConcreteConverter();

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Encoded value cannot be empty');

        $converter->decode('');
    }

    #[Test]
    public function it_throws_exception_for_invalid_character(): void
    {
        $converter = new TestConcreteConverter();

        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Invalid character found: @');

        $converter->decode('ABC@');
    }

    #[Test]
    public function it_performs_round_trip_correctly(): void
    {
        $converter = new TestConcreteConverter();
        $numbers = [0, 1, 10, 100, 1000, 10000, 100000, 1000000];

        foreach ($numbers as $number) {
            $encoded = $converter->encode($number);
            $decoded = $converter->decode($encoded);

            $this->assertEquals($number, $decoded, "Round trip failed for {$number}");
        }
    }

    #[Test]
    public function it_handles_max_int(): void
    {
        $converter = new TestConcreteConverter();

        $encoded = $converter->encode(PHP_INT_MAX);
        $decoded = $converter->decode($encoded);

        $this->assertEquals(PHP_INT_MAX, $decoded);
    }
}

/**
 * Concrete implementation for testing abstract class
 */
final readonly class TestConcreteConverter extends AbstractBaseConverter
{
    private const string CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}
