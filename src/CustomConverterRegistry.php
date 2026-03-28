<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;

/**
 * Registry for custom converter strategies.
 *
 * Allows registering and retrieving custom converter implementations
 * that extend the library's built-in functionality.
 *
 * @example
 * // Register a custom converter
 * CustomConverterRegistry::register(
 *     'hex',
 *     new HexStrategy()
 * );
 *
 * // Retrieve the custom converter
 * $converter = CustomConverterRegistry::get('hex');
 */
final class CustomConverterRegistry
{
    /**
     * @var array<string, IDConverterInterface> Map of custom type names to converter instances
     */
    private static array $converters = [];

    /**
     * Register a custom converter strategy.
     *
     * @param string $name The unique name for the custom converter
     * @param IDConverterInterface $converter The converter instance to register
     *
     * @throws ConverterException If a converter with the same name already exists
     *
     * @example
     * CustomConverterRegistry::register('base16', new HexStrategy());
     */
    public static function register(string $name, IDConverterInterface $converter): void
    {
        if (isset(self::$converters[$name])) {
            throw new ConverterException("Converter '{$name}' is already registered");
        }

        self::$converters[$name] = $converter;
    }

    /**
     * Get a registered custom converter by name.
     *
     * @param string $name The name of the converter to retrieve
     *
     * @return IDConverterInterface The registered converter instance
     *
     * @throws ConverterException If no converter with the given name exists
     *
     * @example
     * $converter = CustomConverterRegistry::get('base16');
     */
    public static function get(string $name): IDConverterInterface
    {
        if (!isset(self::$converters[$name])) {
            throw new ConverterException("Converter '{$name}' is not registered");
        }

        return self::$converters[$name];
    }

    /**
     * Check if a custom converter is registered.
     *
     * @param string $name The name to check
     *
     * @return bool True if the converter is registered, false otherwise
     *
     * @example
     * if (CustomConverterRegistry::has('base16')) {
     *     $converter = CustomConverterRegistry::get('base16');
     * }
     */
    public static function has(string $name): bool
    {
        return isset(self::$converters[$name]);
    }

    /**
     * Unregister a custom converter.
     *
     * @param string $name The name of the converter to remove
     *
     * @return bool True if the converter was removed, false if it didn't exist
     *
     * @example
     * CustomConverterRegistry::unregister('base16');
     */
    public static function unregister(string $name): bool
    {
        if (!isset(self::$converters[$name])) {
            return false;
        }

        unset(self::$converters[$name]);
        return true;
    }

    /**
     * Get all registered custom converter names.
     *
     * @return string[] Array of registered converter names
     *
     * @example
     * $names = CustomConverterRegistry::getRegisteredNames();
     */
    public static function getRegisteredNames(): array
    {
        return array_keys(self::$converters);
    }

    /**
     * Clear all registered custom converters.
     *
     * @return void
     *
     * @example
     * CustomConverterRegistry::clear();
     */
    public static function clear(): void
    {
        self::$converters = [];
    }

    /**
     * Get all registered converters.
     *
     * @return array<string, IDConverterInterface> Map of all registered converters
     *
     * @internal
     */
    public static function getAll(): array
    {
        return self::$converters;
    }
}
