<?php

/**
 * Radix Converter PHP - Usage Examples
 *
 * This file demonstrates how to use the Radix Converter library
 * for encoding and decoding integers using different base systems.
 *
 * @see https://github.com/fatkulnurk/radix-converter-php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;

echo "=== Radix Converter PHP - Usage Examples ===\n\n";

// ============================================================================
// Example 1: Using the Factory (Recommended)
// ============================================================================
echo "1. Using ConverterFactory\n";
echo str_repeat("-", 40) . "\n";

$converter = ConverterFactory::make(ConverterType::BASE62);
$encoded = $converter->encode(12345);
$decoded = $converter->decode($encoded);

echo "Original: 12345\n";
echo "Encoded (Base62): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 2: Direct Strategy Instantiation
// ============================================================================
echo "2. Direct Strategy Instantiation\n";
echo str_repeat("-", 40) . "\n";

$base62 = new Base62Strategy();
$encoded = $base62->encode(999999);
$decoded = $base62->decode($encoded);

echo "Original: 999999\n";
echo "Encoded (Base62): {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 3: All Converter Types
// ============================================================================
echo "3. All Converter Types Comparison\n";
echo str_repeat("-", 40) . "\n";

$number = 123456;

$types = [
    ConverterType::BASE62->value => 'Base62 (0-9, a-z, A-Z)',
    ConverterType::ALPHA_NUMERIC_UPPER->value => 'Alphanumeric Upper (0-9, A-Z)',
    ConverterType::ALPHA_NUMERIC_LOWER->value => 'Alphanumeric Lower (0-9, a-z)',
    ConverterType::ALPHA_ONLY->value => 'Alpha Only (a-z, A-Z)',
];

foreach ($types as $value => $description) {
    $type = ConverterType::from($value);
    $converter = ConverterFactory::make($type);
    $encoded = $converter->encode($number);
    $decoded = $converter->decode($encoded);

    echo "{$description}:\n";
    echo "  Encoded: {$encoded}\n";
    echo "  Decoded: {$decoded}\n";
    echo "  Match: " . ($number === $decoded ? '✓' : '✗') . "\n\n";
}

// ============================================================================
// Example 4: URL Shortener Use Case
// ============================================================================
echo "4. URL Shortener Use Case\n";
echo str_repeat("-", 40) . "\n";

// Simulate converting database ID to short URL code
$databaseId = 154832;
$converter = ConverterFactory::make(ConverterType::BASE62);
$shortCode = $converter->encode($databaseId);

echo "Database ID: {$databaseId}\n";
echo "Short Code: {$shortCode}\n";
echo "Short URL: https://example.com/u/{$shortCode}\n";

// Later, decode to get original ID
$retrievedId = $converter->decode($shortCode);
echo "Retrieved ID: {$retrievedId}\n\n";

// ============================================================================
// Example 5: Error Handling
// ============================================================================
echo "5. Error Handling\n";
echo str_repeat("-", 40) . "\n";

$converter = ConverterFactory::make(ConverterType::BASE62);

// Try to encode a negative number
try {
    $converter->encode(-5);
} catch (ConverterException $e) {
    echo "Error encoding negative: " . $e->getMessage() . "\n";
}

// Try to decode an invalid string
try {
    $converter->decode('');
} catch (ConverterException $e) {
    echo "Error decoding empty: " . $e->getMessage() . "\n";
}

// Try to decode with invalid characters
try {
    $converter->decode('abc!@#');
} catch (ConverterException $e) {
    echo "Error decoding invalid: " . $e->getMessage() . "\n";
}

echo "\n";

// ============================================================================
// Example 6: Round-trip Verification
// ============================================================================
echo "6. Round-trip Verification (Large Numbers)\n";
echo str_repeat("-", 40) . "\n";

$testNumbers = [0, 1, 100, 1000, PHP_INT_MAX];
$converter = ConverterFactory::make(ConverterType::BASE62);

foreach ($testNumbers as $num) {
    $encoded = $converter->encode($num);
    $decoded = $converter->decode($encoded);
    $status = ($num === $decoded) ? '✓' : '✗';
    echo "{$status} {$num} -> {$encoded} -> {$decoded}\n";
}

echo "\n=== Examples Complete ===\n";
