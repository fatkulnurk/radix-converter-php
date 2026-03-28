<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Strategies\AlphanumericUpperStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AlphanumericUpperStrategyTest extends TestCase
{
    private AlphanumericUpperStrategy $converter;

    protected function setUp(): void
    {
        $this->converter = new AlphanumericUpperStrategy();
    }

    #[Test]
    public function it_uses_uppercase_charset(): void
    {
        $encoded = $this->converter->encode(35);

        $this->assertEquals('Z', $encoded);
    }

    #[Test]
    public function it_encodes_zero(): void
    {
        $this->assertEquals('0', $this->converter->encode(0));
    }

    #[Test]
    public function it_encodes_number(): void
    {
        $this->assertEquals('RS', $this->converter->encode(1000));
    }

    #[Test]
    public function it_decodes_number(): void
    {
        $this->assertEquals(1000, $this->converter->decode('RS'));
    }

    #[Test]
    public function it_round_trips_correctly(): void
    {
        $numbers = [0, 1, 10, 35, 36, 100, 1000, 10000];

        foreach ($numbers as $number) {
            $encoded = $this->converter->encode($number);
            $decoded = $this->converter->decode($encoded);
            $this->assertEquals($number, $decoded);
        }
    }
}
