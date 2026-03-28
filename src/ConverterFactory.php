<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericLowerStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericUpperStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphaOnlyStrategy;
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;

final class ConverterFactory
{
    public static function make(ConverterType $type): IDConverterInterface
    {
        return match ($type) {
            ConverterType::BASE62 => new Base62Strategy(),
            ConverterType::ALPHA_NUMERIC_UPPER => new AlphanumericUpperStrategy(),
            ConverterType::ALPHA_NUMERIC_LOWER => new AlphanumericLowerStrategy(),
            ConverterType::ALPHA_ONLY => new AlphaOnlyStrategy(),
        };
    }
}