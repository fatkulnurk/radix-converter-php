<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericLowerStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericUpperStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphaOnlyStrategy;
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;

/**
 * Factory class for creating radix converter instances.
 *
 * This factory provides a convenient way to instantiate the appropriate
 * converter strategy based on the desired converter type.
 *
 * @example
 * // Create a Base62 converter
 * $converter = ConverterFactory::make(ConverterType::BASE62);
 * echo $converter->encode(12345); // Output: "3D7"
 *
 * @example
 * // Create an alphanumeric lowercase converter
 * $converter = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);
 * echo $converter->encode(1000); // Output: "rs"
 */
final class ConverterFactory
{
    /**
     * Create a new converter instance based on the specified type.
     *
     * @param ConverterType $type The type of converter to create
     *
     * @return IDConverterInterface The converter instance implementing the specified strategy
     *
     * @example
     * // Using the factory to create different converters
     * $base62 = ConverterFactory::make(ConverterType::BASE62);
     * $alphaUpper = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_UPPER);
     * $alphaLower = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);
     * $alphaOnly = ConverterFactory::make(ConverterType::ALPHA_ONLY);
     */
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