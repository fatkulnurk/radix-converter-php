<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

/**
 * Base62 converter implementation.
 *
 * Uses a charset of 62 characters: 0-9, a-z, A-Z
 * This is commonly used for URL shorteners, unique ID generation,
 * and compact string representations of large integers.
 *
 * @example
 * // Create a Base62 converter
 * $converter = new Base62Strategy();
 *
 * // Encode an integer
 * $encoded = $converter->encode(12345); // "3D7"
 *
 * // Decode back to integer
 * $decoded = $converter->decode("3D7"); // 12345
 *
 * @see AbstractBaseConverter
 */
final readonly class Base62Strategy extends AbstractBaseConverter
{
    /**
     * The Base62 charset: digits, lowercase, and uppercase letters.
     */
    private const string CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}