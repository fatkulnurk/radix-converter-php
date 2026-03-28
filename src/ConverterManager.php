<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericLowerStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphanumericUpperStrategy;
use Fatkulnurk\RadixConverter\Strategies\AlphaOnlyStrategy;
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;

/**
 * Manager class for radix converter instances.
 *
 * This is a non-static alternative to ConverterFactory, designed for:
 * - Dependency Injection containers
 * - Laravel Octane / Swoole environments
 * - Test mocking
 *
 * The manager maintains its own registry of converters and does not
 * share state across requests, making it safe for long-running processes.
 *
 * @example
 * // Use in a service class
 * class MyService {
 *     public function __construct(
 *         private ConverterManager $converterManager
 *     ) {}
 *
 *     public function process(int $id): string
 *     {
 *         return $this->converterManager->encode(ConverterType::BASE62, $id);
 *     }
 * }
 */
final class ConverterManager
{
    /**
     * @var array<string, IDConverterInterface> Cache of converter instances
     */
    private array $converters = [];

    /**
     * @var array<string, IDConverterInterface> Custom registered converters
     */
    private array $customConverters = [];

    /**
     * Create a new converter manager instance.
     *
     * @param array<string, IDConverterInterface> $customConverters Pre-registered custom converters
     *
     * @example
     * $manager = new ConverterManager([
     *     'hex' => new HexStrategy(),
     * ]);
     */
    public function __construct(array $customConverters = [])
    {
        foreach ($customConverters as $name => $converter) {
            $this->registerCustom($name, $converter);
        }
    }

    /**
     * Get or create a converter instance.
     *
     * @param ConverterType|string $type The converter type or custom converter name
     *
     * @return IDConverterInterface The converter instance
     *
     * @throws ConverterException If the converter type is not recognized
     *
     * @example
     * $converter = $manager->get(ConverterType::BASE62);
     * $custom = $manager->get('hex');
     */
    public function get(ConverterType|string $type): IDConverterInterface
    {
        $key = \is_string($type) ? $type : $type->value;

        // Check custom converters first
        if (\is_string($type) && isset($this->customConverters[$key])) {
            return $this->customConverters[$key];
        }

        // Return cached instance if available
        if (isset($this->converters[$key])) {
            return $this->converters[$key];
        }

        // Create new converter
        $converter = $this->createConverter($type);
        $this->converters[$key] = $converter;

        return $converter;
    }

    /**
     * Encode a number using the specified converter type.
     *
     * @param ConverterType|string $type The converter type
     * @param int $number The number to encode
     *
     * @return string The encoded string
     *
     * @throws ConverterException If the number is negative or converter type is invalid
     *
     * @example
     * $encoded = $manager->encode(ConverterType::BASE62, 12345);
     */
    public function encode(ConverterType|string $type, int $number): string
    {
        return $this->get($type)->encode($number);
    }

    /**
     * Decode a string using the specified converter type.
     *
     * @param ConverterType|string $type The converter type
     * @param string $encoded The encoded string
     *
     * @return int The decoded number
     *
     * @throws ConverterException If the string is invalid or converter type is invalid
     *
     * @example
     * $decoded = $manager->decode(ConverterType::BASE62, '3d7');
     */
    public function decode(ConverterType|string $type, string $encoded): int
    {
        return $this->get($type)->decode($encoded);
    }

    /**
     * Register a custom converter at runtime.
     *
     * @param string $name The unique name for the custom converter
     * @param IDConverterInterface $converter The converter instance
     *
     * @throws ConverterException If a converter with the same name already exists
     *
     * @example
     * $manager->registerCustom('hex', new HexStrategy());
     */
    public function registerCustom(string $name, IDConverterInterface $converter): void
    {
        if (isset($this->customConverters[$name])) {
            throw new ConverterException("Converter '{$name}' is already registered");
        }

        $this->customConverters[$name] = $converter;
    }

    /**
     * Check if a custom converter is registered.
     *
     * @param string $name The converter name to check
     *
     * @return bool True if registered, false otherwise
     *
     * @example
     * if ($manager->hasCustom('hex')) { ... }
     */
    public function hasCustom(string $name): bool
    {
        return isset($this->customConverters[$name]);
    }

    /**
     * Get all registered custom converter names.
     *
     * @return string[] Array of custom converter names
     *
     * @example
     * $names = $manager->getCustomNames();
     */
    public function getCustomNames(): array
    {
        return array_keys($this->customConverters);
    }

    /**
     * Clear all cached converter instances.
     *
     * This is useful for long-running processes to free memory.
     * Built-in converters will be recreated on next use.
     * Custom converters are preserved.
     *
     * @return void
     *
     * @example
     * $manager->clearCache();
     */
    public function clearCache(): void
    {
        $this->converters = [];
    }

    /**
     * Clear all converters including custom ones.
     *
     * @return void
     *
     * @example
     * $manager->clearAll();
     */
    public function clearAll(): void
    {
        $this->converters = [];
        $this->customConverters = [];
    }

    /**
     * Create a new converter instance based on type.
     *
     * @param ConverterType|string $type The converter type
     *
     * @return IDConverterInterface The converter instance
     *
     * @throws ConverterException If the converter type is not recognized
     *
     * @internal
     */
    private function createConverter(ConverterType|string $type): IDConverterInterface
    {
        if ($type instanceof ConverterType) {
            return match ($type) {
                ConverterType::BASE62 => new Base62Strategy(),
                ConverterType::ALPHA_NUMERIC_UPPER => new AlphanumericUpperStrategy(),
                ConverterType::ALPHA_NUMERIC_LOWER => new AlphanumericLowerStrategy(),
                ConverterType::ALPHA_ONLY => new AlphaOnlyStrategy(),
            };
        }

        throw new ConverterException("Unknown converter type: {$type}");
    }
}
