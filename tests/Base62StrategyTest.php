<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class Base62StrategyTest extends TestCase
{
    private Base62Strategy $converter;

    protected function setUp(): void
    {
        $this->converter = new Base62Strategy();
    }

    #[Test]
    public function it_uses_correct_charset(): void
    {
        $encoded = $this->converter->encode(61);

        $this->assertEquals('Z', $encoded);
    }

    #[Test]
    public function it_encodes_zero(): void
    {
        $this->assertEquals('0', $this->converter->encode(0));
    }

    #[Test]
    public function it_encodes_single_digit(): void
    {
        $this->assertEquals('5', $this->converter->encode(5));
    }

    #[Test]
    public function it_encodes_lowercase_letter(): void
    {
        $this->assertEquals('a', $this->converter->encode(10));
    }

    #[Test]
    public function it_encodes_uppercase_letter(): void
    {
        $this->assertEquals('A', $this->converter->encode(36));
    }

    #[Test]
    public function it_encodes_typical_number(): void
    {
        $this->assertEquals('3d7', $this->converter->encode(12345));
    }

    #[Test]
    public function it_decodes_zero(): void
    {
        $this->assertEquals(0, $this->converter->decode('0'));
    }

    #[Test]
    public function it_decodes_lowercase_letter(): void
    {
        $this->assertEquals(10, $this->converter->decode('a'));
    }

    #[Test]
    public function it_decodes_uppercase_letter(): void
    {
        $this->assertEquals(36, $this->converter->decode('A'));
    }

    #[Test]
    public function it_decodes_typical_number(): void
    {
        $this->assertEquals(12345, $this->converter->decode('3d7'));
    }

    #[Test]
    public function it_round_trips_correctly(): void
    {
        $numbers = [0, 1, 10, 36, 62, 100, 1000, 12345, 1000000];

        foreach ($numbers as $number) {
            $encoded = $this->converter->encode($number);
            $decoded = $this->converter->decode($encoded);
            $this->assertEquals($number, $decoded);
        }
    }
}
