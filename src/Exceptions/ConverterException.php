<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Exceptions;

/**
 * Exception thrown when a conversion error occurs.
 *
 * This exception is raised during encoding or decoding operations when:
 * - A negative number is provided for encoding
 * - An empty string is provided for decoding
 * - An invalid character is encountered during decoding
 *
 * @extends \InvalidArgumentException
 */
class ConverterException extends \InvalidArgumentException
{
}