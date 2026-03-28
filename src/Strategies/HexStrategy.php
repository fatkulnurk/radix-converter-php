<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

/**
 * Hexadecimal (Base16) converter implementation.
 *
 * Uses a charset of 16 characters: 0-9, a-f
 * This is an example of a custom converter that can be registered
 * with the CustomConverterRegistry.
 *
 * @example
 * // Register the custom converter
 * use Fatkulnurk\RadixConverter\CustomConverterRegistry;
 *
 * CustomConverterRegistry::register('hex', new HexStrategy());
 *
 * // Use the custom converter
 * $converter = CustomConverterRegistry::get('hex');
 * echo $converter->encode(255); // "ff"
 * echo $converter->decode('ff'); // 255
 *
 * @see AbstractBaseConverter
 */
final readonly class HexStrategy extends AbstractBaseConverter
{
    /**
     * The hexadecimal charset: digits 0-9 and lowercase letters a-f.
     */
    private const string CHARSET = '0123456789abcdef';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}
