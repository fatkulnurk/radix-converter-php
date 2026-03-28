<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Tests;

use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConverterExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_invalid_argument_exception(): void
    {
        $exception = new ConverterException('Test message');

        $this->assertInstanceOf(\InvalidArgumentException::class, $exception);
    }

    #[Test]
    public function it_has_correct_message(): void
    {
        $exception = new ConverterException('Custom error message');

        $this->assertEquals('Custom error message', $exception->getMessage());
    }

    #[Test]
    public function it_can_be_thrown(): void
    {
        $this->expectException(ConverterException::class);
        $this->expectExceptionMessage('Something went wrong');

        throw new ConverterException('Something went wrong');
    }
}
