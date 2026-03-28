<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Enums;

/**
 * Enumeration of available radix converter types.
 *
 * Each case represents a different character set and base for encoding/decoding:
 * - BASE62: Uses 0-9, a-z, A-Z (base 62)
 * - ALPHA_NUMERIC_UPPER: Uses 0-9, A-Z (base 36)
 * - ALPHA_NUMERIC_LOWER: Uses 0-9, a-z (base 36)
 * - ALPHA_ONLY: Uses a-z, A-Z (base 52)
 */
enum ConverterType: string
{
    /**
     * Base62 encoding using characters: 0-9, a-z, A-Z
     * Commonly used for URL shorteners and unique ID generation.
     */
    case BASE62 = 'base62';

    /**
     * Alphanumeric encoding with uppercase letters: 0-9, A-Z
     * Base 36 encoding with uppercase alphabet.
     */
    case ALPHA_NUMERIC_UPPER = 'alphanumeric_upper';

    /**
     * Alphanumeric encoding with lowercase letters: 0-9, a-z
     * Base 36 encoding with lowercase alphabet.
     */
    case ALPHA_NUMERIC_LOWER = 'alphanumeric_lower';

    /**
     * Alpha-only encoding using letters: a-z, A-Z
     * Base 52 encoding without numeric characters.
     */
    case ALPHA_ONLY = 'alpha_only';
}
