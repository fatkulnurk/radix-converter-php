<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

/**
 * Alpha-only converter implementation.
 *
 * Uses a charset of 52 characters: a-z, A-Z
 * This provides base-52 encoding using only alphabetic characters,
 * useful when numeric characters need to be excluded from identifiers.
 *
 * @example
 * // Create an alpha-only converter
 * $converter = new AlphaOnlyStrategy();
 *
 * // Encode an integer
 * $encoded = $converter->encode(100); // "cU"
 *
 * // Decode back to integer
 * $decoded = $converter->decode("cU"); // 100
 *
 * @see AbstractBaseConverter
 */
final readonly class AlphaOnlyStrategy extends AbstractBaseConverter
{
    /**
     * The alpha-only charset: lowercase and uppercase letters (no digits).
     */
    private const string CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}