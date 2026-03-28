<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Strategies\HexStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HexStrategyTest extends TestCase
{
    private HexStrategy $converter;

    protected function setUp(): void
    {
        $this->converter = new HexStrategy();
    }

    #[Test]
    public function it_uses_hex_charset(): void
    {
        $encoded = $this->converter->encode(15);

        $this->assertEquals('f', $encoded);
    }

    #[Test]
    public function it_encodes_zero(): void
    {
        $this->assertEquals('0', $this->converter->encode(0));
    }

    #[Test]
    public function it_encodes_255(): void
    {
        $this->assertEquals('ff', $this->converter->encode(255));
    }

    #[Test]
    public function it_encodes_4096(): void
    {
        $this->assertEquals('1000', $this->converter->encode(4096));
    }

    #[Test]
    public function it_decodes_ff(): void
    {
        $this->assertEquals(255, $this->converter->decode('ff'));
    }

    #[Test]
    public function it_decodes_1000(): void
    {
        $this->assertEquals(4096, $this->converter->decode('1000'));
    }

    #[Test]
    public function it_round_trips_correctly(): void
    {
        $numbers = [0, 1, 10, 15, 16, 255, 256, 1000, 4096];

        foreach ($numbers as $number) {
            $encoded = $this->converter->encode($number);
            $decoded = $this->converter->decode($encoded);
            $this->assertEquals($number, $decoded);
        }
    }
}
