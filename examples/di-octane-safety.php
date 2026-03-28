<?php

/**
 * Memory Safety & Dependency Injection Example
 *
 * This file demonstrates how to use the library safely with:
 * - Dependency Injection containers
 * - Laravel Octane / Swoole
 * - Long-running processes
 *
 * @see https://github.com/fatkulnurk/radix-converter-php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Fatkulnurk\RadixConverter\ConverterManager;
use Fatkulnurk\RadixConverter\CustomConverterRegistry;
use Fatkulnurk\RadixConverter\Enums\ConverterType;
use Fatkulnurk\RadixConverter\Strategies\HexStrategy;

echo "=== Memory Safety & DI Examples ===\n\n";

// ============================================================================
// Example 1: Using ConverterManager (Recommended for DI)
// ============================================================================
echo "1. Using ConverterManager for Dependency Injection\n";
echo str_repeat("-", 50) . "\n";

// Create manager instance (inject this via container)
$manager = new ConverterManager();

// Encode/decode using the manager
$encoded = $manager->encode(ConverterType::BASE62, 12345);
$decoded = $manager->decode(ConverterType::BASE62, $encoded);

echo "Encoded: {$encoded}\n";
echo "Decoded: {$decoded}\n\n";

// ============================================================================
// Example 2: ConverterManager with Custom Converters
// ============================================================================
echo "2. ConverterManager with Custom Converters\n";
echo str_repeat("-", 50) . "\n";

// Register custom converters via constructor
$manager = new ConverterManager([
    'hex' => new HexStrategy(),
]);

$encoded = $manager->encode('hex', 255);
$decoded = $manager->decode('hex', $encoded);

echo "Hex Encoded: {$encoded}\n";
echo "Hex Decoded: {$decoded}\n\n";

// ============================================================================
// Example 3: Simulating Request Isolation (Octane/Swoole Safe)
// ============================================================================
echo "3. Request Isolation Simulation (Octane/Swoole Safe)\n";
echo str_repeat("-", 50) . "\n";

// Each request gets its own manager instance
function simulateRequest(int $requestId): void
{
    // New manager per request - no shared state
    $manager = new ConverterManager();
    $encoded = $manager->encode(ConverterType::BASE62, $requestId * 100);
    echo "Request {$requestId}: Encoded {$requestId} -> {$encoded}\n";
}

// Simulate multiple requests
for ($i = 1; $i <= 3; $i++) {
    simulateRequest($i);
}

echo "Each request has isolated state - safe for Octane/Swoole!\n\n";

// ============================================================================
// Example 4: Dependency Injection Pattern
// ============================================================================
echo "4. Dependency Injection Pattern\n";
echo str_repeat("-", 50) . "\n";

// Service class that depends on ConverterManager
class UrlShortenerService
{
    public function __construct(
        private ConverterManager $converterManager
    ) {
    }

    public function shorten(int $id): string
    {
        return $this->converterManager->encode(ConverterType::BASE62, $id);
    }

    public function expand(string $code): int
    {
        return $this->converterManager->decode(ConverterType::BASE62, $code);
    }
}

// Create service with injected dependency
$manager = new ConverterManager();
$shortener = new UrlShortenerService($manager);

$shortCode = $shortener->shorten(12345);
$originalId = $shortener->expand($shortCode);

echo "Shortened: 12345 -> {$shortCode}\n";
echo "Expanded: {$shortCode} -> {$originalId}\n\n";

// ============================================================================
// Example 5: Memory Management in Long-Running Processes
// ============================================================================
echo "5. Memory Management in Long-Running Processes\n";
echo str_repeat("-", 50) . "\n";

$manager = new ConverterManager();

// Use converters
$manager->encode(ConverterType::BASE62, 1000);
$manager->encode(ConverterType::ALPHA_NUMERIC_LOWER, 2000);

// Memory before clear
$memoryBefore = memory_get_usage(true);

// Clear cache to free memory (useful in long-running processes)
$manager->clearCache();

// Memory after clear
$memoryAfter = memory_get_usage(true);

echo "Memory before clear: " . number_format($memoryBefore) . " bytes\n";
echo "Memory after clear: " . number_format($memoryAfter) . " bytes\n";
echo "Freed: " . number_format($memoryBefore - $memoryAfter) . " bytes\n\n";

// ============================================================================
// Example 6: Why Static Registry is NOT Safe for Octane/Swoole
// ============================================================================
echo "6. Static Registry Warning (NOT for Octane/Swoole)\n";
echo str_repeat("-", 50) . "\n";

echo "CustomConverterRegistry uses static storage:\n";
echo "- Data persists across requests in Swoole/Octane\n";
echo "- Request 1's data can leak to Request 2\n";
echo "- NOT recommended for long-running processes\n\n";

echo "DO THIS INSTEAD:\n";
echo "- Use ConverterManager (instance-based)\n";
echo "- Inject via constructor\n";
echo "- Each request gets fresh instance\n\n";

// ============================================================================
// Example 7: Factory Comparison
// ============================================================================
echo "7. Factory vs Manager Comparison\n";
echo str_repeat("-", 50) . "\n";

use Fatkulnurk\RadixConverter\ConverterFactory;

// Factory - creates new instance each time (no caching)
$converter1 = ConverterFactory::make(ConverterType::BASE62);
$converter2 = ConverterFactory::make(ConverterType::BASE62);
echo "Factory: Same instance? " . ($converter1 === $converter2 ? 'Yes' : 'No') . " (creates new each time)\n";

// Manager - caches instances (more efficient)
$manager = new ConverterManager();
$c1 = $manager->get(ConverterType::BASE62);
$c2 = $manager->get(ConverterType::BASE62);
echo "Manager: Same instance? " . ($c1 === $c2 ? 'Yes' : 'No') . " (cached)\n\n";

echo "=== Examples Complete ===\n";
