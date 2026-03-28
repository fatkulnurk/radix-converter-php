<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Contracts;

/**
 * Interface for radix (base-N) converter implementations.
 *
 * This interface defines the contract for converting integers to and from
 * different base representations (e.g., Base62, alphanumeric, etc.).
 */
interface IDConverterInterface
{
    /**
     * Encode an integer into a string representation using the specific base.
     *
     * @param int $number The non-negative integer to encode
     *
     * @return string The encoded string representation
     *
     * @throws \Fatkulnurk\RadixConverter\Exceptions\ConverterException If the number is negative
     *
     * @example
     * // Base62 encoding
     * $converter->encode(12345); // returns "3D7"
     */
    public function encode(int $number): string;

    /**
     * Decode a string representation back to an integer.
     *
     * @param string $encoded The encoded string to decode
     *
     * @return int The decoded integer value
     *
     * @throws \Fatkulnurk\RadixConverter\Exceptions\ConverterException If the string contains invalid characters or is empty
     *
     * @example
     * // Base62 decoding
     * $converter->decode("3D7"); // returns 12345
     */
    public function decode(string $encoded): int;
}