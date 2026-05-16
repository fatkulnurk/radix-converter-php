<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;

/**
 * Abstract base class for radix (base-N) converter implementations.
 *
 * This class provides the core encoding and decoding logic for converting
 * integers to and from different base representations. Concrete implementations
 * must define their specific character set via the {@see getCharset()} method.
 *
 * The encoding algorithm uses repeated division by the base, while decoding
 * uses positional notation multiplication.
 *
 * @see IDConverterInterface
 */
abstract readonly class AbstractBaseConverter implements IDConverterInterface
{
    /**
     * The character set used for encoding/decoding.
     * Each character represents a digit in the target base.
     */
    protected readonly string $charset;

    /**
     * The base (radix) value, calculated from the charset length.
     * For example, Base62 has a base of 62.
     */
    protected readonly int $base;

    /**
     * Initialize the converter with the charset from the subclass.
     *
     * The base is automatically calculated as the length of the charset.
     */
    public function __construct()
    {
        $this->charset = $this->getCharset();
        $this->base = \strlen($this->charset);
    }

    /**
     * Get the character set for this converter implementation.
     *
     * Each subclass must define its own charset that determines the base
     * and the characters used for representation.
     *
     * @return string The charset string where position represents the digit value
     *
     * @example
     * // Base62 charset
     * return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     */
    abstract protected function getCharset(): string;

    /**
     * Encode an integer into a string representation using the specific base.
     *
     * The encoding process repeatedly divides the number by the base and
     * uses the remainder to select characters from the charset.
     *
     * @param int $number The non-negative integer to encode
     *
     * @return string The encoded string representation
     *
     * @throws ConverterException If the number is negative
     *
     * @example
     * // Encoding the number 12345 in Base62
     * $converter->encode(12345); // Returns "3d7"
     *
     * @example
     * // Encoding zero returns the first character of charset
     * $converter->encode(0); // Returns "0"
     */
    #[\Override]
    public function encode(int $number): string
    {
        if ($number < 0) {
            throw new ConverterException('Input number must be positive');
        }

        if ($number === 0) {
            return $this->charset[0];
        }

        $result = '';
        while ($number > 0) {
            $remainder = $number % $this->base;
            $result = $this->charset[$remainder] . $result;
            $number = \intdiv($number, $this->base);
        }

        return $result;
    }

    /**
     * Decode a string representation back to an integer.
     *
     * The decoding process uses positional notation, multiplying the accumulated
     * result by the base and adding the position value of each character.
     *
     * @param string $encoded The encoded string to decode
     *
     * @return int The decoded integer value
     *
     * @throws ConverterException If the string is empty or contains invalid characters
     *
     * @example
     * // Decoding a Base62 string
     * $converter->decode("3d7"); // Returns 12345
     *
     * @example
     * // Decoding the first charset character
     * $converter->decode("0"); // Returns 0
     */
    #[\Override]
    public function decode(string $encoded): int
    {
        if ($encoded === '') {
            throw new ConverterException('Encoded value cannot be empty');
        }

        $result = 0;
        $length = \strlen($encoded);

        for ($i = 0; $i < $length; $i++) {
            $char = $encoded[$i];
            $position = \strpos($this->charset, $char);

            if ($position === false) {
                throw new ConverterException("Invalid character found: {$char}");
            }

            $result = ($result * $this->base) + $position;
        }

        return $result;
    }
}