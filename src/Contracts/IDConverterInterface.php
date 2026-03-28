<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Contracts;

interface IDConverterInterface
{
    public function encode(int $number): string;

    public function decode(string $encoded): int;
}