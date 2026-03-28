<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Strategies\AlphaOnlyStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AlphaOnlyStrategyTest extends TestCase
{
    private AlphaOnlyStrategy $converter;

    protected function setUp(): void
    {
        $this->converter = new AlphaOnlyStrategy();
    }

    #[Test]
    public function it_uses_alpha_only_charset(): void
    {
        $encoded = $this->converter->encode(51);

        $this->assertEquals('Z', $encoded);
    }

    #[Test]
    public function it_encodes_zero(): void
    {
        $this->assertEquals('a', $this->converter->encode(0));
    }

    #[Test]
    public function it_encodes_number(): void
    {
        $this->assertEquals('bW', $this->converter->encode(100));
    }

    #[Test]
    public function it_decodes_number(): void
    {
        $this->assertEquals(100, $this->converter->decode('bW'));
    }

    #[Test]
    public function it_round_trips_correctly(): void
    {
        $numbers = [0, 1, 10, 25, 51, 52, 100, 1000];

        foreach ($numbers as $number) {
            $encoded = $this->converter->encode($number);
            $decoded = $this->converter->decode($encoded);
            $this->assertEquals($number, $decoded);
        }
    }
}
