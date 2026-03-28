<?php

/**
 * Custom Converter Strategy Example
 *
 * This file demonstrates how to create and register custom converter
 * strategies that extend the library's built-in functionality.
 *
 * @see https://github.com/fatkulnurk/radix-converter-php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\CustomConverterRegistry;
use Fatkulnurk\RadixConverter\Strategies\AbstractBaseConverter;

echo "=== Custom Converter Strategy Examples ===\n\n";

// ============================================================================
// Example 1: Using the Built-in HexStrategy
// ============================================================================
echo "1. Using Built-in HexStrategy\n";
echo str_repeat("-", 40) . "\n";

// Register the hex converter
CustomConverterRegistry::register('hex', new \Fatkulnurk\RadixConverter\Strategies\HexStrategy());

$converter = CustomConverterRegistry::get('hex');

$number = 255;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (Hex): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 2: Using Factory with Custom Converter
// ============================================================================
echo "2. Using Factory with Custom Converter\n";
echo str_repeat("-", 40) . "\n";

// The factory can also retrieve custom converters by name
$converter = ConverterFactory::make('hex');

$number = 4096;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (Hex via Factory): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 3: Creating a Custom Binary (Base2) Converter
// ============================================================================
echo "3. Creating Custom Binary (Base2) Converter\n";
echo str_repeat("-", 40) . "\n";

final readonly class BinaryStrategy extends AbstractBaseConverter
{
    private const string CHARSET = '01';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}

// Register the binary converter
CustomConverterRegistry::register('binary', new BinaryStrategy());

$converter = ConverterFactory::make('binary');

$number = 42;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (Binary): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 4: Creating a Custom Octal (Base8) Converter
// ============================================================================
echo "4. Creating Custom Octal (Base8) Converter\n";
echo str_repeat("-", 40) . "\n";

final readonly class OctalStrategy extends AbstractBaseConverter
{
    private const string CHARSET = '01234567';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}

CustomConverterRegistry::register('octal', new OctalStrategy());

$converter = ConverterFactory::make('octal');

$number = 512;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (Octal): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 5: Creating a Custom Base64 Converter
// ============================================================================
echo "5. Creating Custom Base64 Converter\n";
echo str_repeat("-", 40) . "\n";

final readonly class Base64Strategy extends AbstractBaseConverter
{
    private const string CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

    #[\Override]
    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}

CustomConverterRegistry::register('base64', new Base64Strategy());

$converter = ConverterFactory::make('base64');

$number = 65535;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (Base64): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 6: Checking Registered Converters
// ============================================================================
echo "6. Checking Registered Converters\n";
echo str_repeat("-", 40) . "\n";

$registeredNames = CustomConverterRegistry::getRegisteredNames();
echo "Registered converters: " . implode(', ', $registeredNames) . "\n\n";

foreach ($registeredNames as $name) {
    $has = CustomConverterRegistry::has($name);
    echo "  {$name}: " . ($has ? 'registered' : 'not registered') . "\n";
}

echo "\n";

// ============================================================================
// Example 7: Unregistering a Converter
// ============================================================================
echo "7. Unregistering a Converter\n";
echo str_repeat("-", 40) . "\n";

CustomConverterRegistry::unregister('binary');
echo "Unregistered 'binary'\n";

$registeredNames = CustomConverterRegistry::getRegisteredNames();
echo "Remaining converters: " . implode(', ', $registeredNames) . "\n\n";

// ============================================================================
// Example 8: Creating Converter from External Class
// ============================================================================
echo "8. Creating Converter from External Class\n";
echo str_repeat("-", 40) . "\n";

// Simulating an external class that implements the interface
class ExternalCustomConverter implements IDConverterInterface
{
    public function encode(int $number): string
    {
        // Simple example: prefix with "X" and convert to uppercase hex
        return 'X' . strtoupper(dechex($number));
    }

    public function decode(string $encoded): int
    {
        // Remove prefix and convert from hex
        $hex = substr($encoded, 1);
        return hexdec($hex);
    }
}

CustomConverterRegistry::register('external', new ExternalCustomConverter());

$converter = ConverterFactory::make('external');

$number = 12345;
$encoded = $converter->encode($number);
$decoded = $converter->decode($encoded);

echo "Original: {$number}\n";
echo "Encoded (External): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 9: Clearing All Registered Converters
// ============================================================================
echo "9. Clearing All Registered Converters\n";
echo str_repeat("-", 40) . "\n";

CustomConverterRegistry::clear();
$registeredNames = CustomConverterRegistry::getRegisteredNames();
echo "After clear, registered converters: " . (empty($registeredNames) ? 'none' : implode(', ', $registeredNames)) . "\n\n";

echo "=== Examples Complete ===\n";
