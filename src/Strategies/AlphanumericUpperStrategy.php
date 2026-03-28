<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

/**
 * Alphanumeric uppercase converter implementation.
 *
 * Uses a charset of 36 characters: 0-9, A-Z
 * This provides base-36 encoding with uppercase letters only,
 * useful for generating compact, case-insensitive identifiers.
 *
 * @example
 * // Create an alphanumeric uppercase converter
 * $converter = new AlphanumericUpperStrategy();
 *
 * // Encode an integer
 * $encoded = $converter->encode(1000); // "RS"
 *
 * // Decode back to integer
 * $decoded = $converter->decode("RS"); // 1000
 *
 * @see AbstractBaseConverter
 */
final readonly class AlphanumericUpperStrategy extends AbstractBaseConverter
{
    /**
     * The alphanumeric uppercase charset: digits and uppercase letters.
     */
    private const string CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}