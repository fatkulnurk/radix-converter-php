<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

/**
 * Alphanumeric lowercase converter implementation.
 *
 * Uses a charset of 36 characters: 0-9, a-z
 * This provides base-36 encoding with lowercase letters only,
 * useful for generating compact identifiers that are easy to type.
 *
 * @example
 * // Create an alphanumeric lowercase converter
 * $converter = new AlphanumericLowerStrategy();
 *
 * // Encode an integer
 * $encoded = $converter->encode(1000); // "rs"
 *
 * // Decode back to integer
 * $decoded = $converter->decode("rs"); // 1000
 *
 * @see AbstractBaseConverter
 */
final readonly class AlphanumericLowerStrategy extends AbstractBaseConverter
{
    /**
     * The alphanumeric lowercase charset: digits and lowercase letters.
     */
    private const string CHARSET = '0123456789abcdefghijklmnopqrstuvwxyz';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}