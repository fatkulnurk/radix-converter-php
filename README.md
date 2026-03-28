# Radix Converter PHP

A type-safe radix (base-N) converter library for PHP 8.5+. Convert integers to and from different base representations.

[Packagist](https://packagist.org/packages/fatkulnurk/radix-converter)

## Installation

```bash
composer require fatkulnurk/radix-converter
```

## Requirements

- PHP 8.5 or higher
- Composer

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

$converter = ConverterFactory::make(ConverterType::BASE62);

$encoded = $converter->encode(12345); // "3d7"
$decoded = $converter->decode($encoded); // 12345
```

## Available Converter Types

| Type | Charset | Base |
|------|---------|------|
| `BASE62` | `0-9, a-z, A-Z` | 62 |
| `ALPHA_NUMERIC_UPPER` | `0-9, A-Z` | 36 |
| `ALPHA_NUMERIC_LOWER` | `0-9, a-z` | 36 |
| `ALPHA_ONLY` | `a-z, A-Z` | 52 |

## Usage Examples

### Using the Factory

```php
use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

$base62 = ConverterFactory::make(ConverterType::BASE62);
echo $base62->encode(12345); // "3d7"

$alphaUpper = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_UPPER);
echo $alphaUpper->encode(1000); // "RS"

$alphaLower = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);
echo $alphaLower->encode(1000); // "rs"

$alphaOnly = ConverterFactory::make(ConverterType::ALPHA_ONLY);
echo $alphaOnly->encode(100); // "cU"
```

### Direct Strategy Instantiation

```php
use Fatkulnurk\RadixConverter\Strategies\Base62Strategy;

$converter = new Base62Strategy();
$encoded = $converter->encode(999999);
$decoded = $converter->decode($encoded);
```

### URL Shortener Example

```php
use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

$converter = ConverterFactory::make(ConverterType::BASE62);

$databaseId = 154832;
$shortCode = $converter->encode($databaseId);
$shortUrl = "https://example.com/u/{$shortCode}";

$originalId = $converter->decode($shortCode);
```

### Error Handling

```php
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;

try {
    $converter->encode(-5);
} catch (ConverterException $e) {
    echo $e->getMessage(); // "Input number must be positive"
}

try {
    $converter->decode('');
} catch (ConverterException $e) {
    echo $e->getMessage(); // "Encoded value cannot be empty"
}

try {
    $converter->decode('abc!@#');
} catch (ConverterException $e) {
    echo $e->getMessage(); // "Invalid character found: !"
}
```

## Custom Strategies

You can create and register custom converter strategies for any base system.

### Using Built-in HexStrategy

```php
use Fatkulnurk\RadixConverter\CustomConverterRegistry;
use Fatkulnurk\RadixConverter\ConverterFactory;

// Register the hex converter
CustomConverterRegistry::register('hex', new \Fatkulnurk\RadixConverter\Strategies\HexStrategy());

// Use via registry
$converter = CustomConverterRegistry::get('hex');
echo $converter->encode(255); // "ff"

// Or use via factory
$converter = ConverterFactory::make('hex');
echo $converter->encode(255); // "ff"
```

### Creating Your Own Strategy

```php
use Fatkulnurk\RadixConverter\Strategies\AbstractBaseConverter;
use Fatkulnurk\RadixConverter\CustomConverterRegistry;

// Create a binary (base-2) converter
final readonly class BinaryStrategy extends AbstractBaseConverter
{
    private const string CHARSET = '01';

    protected function getCharset(): string
    {
        return self::CHARSET;
    }
}

// Register and use
CustomConverterRegistry::register('binary', new BinaryStrategy());
$converter = ConverterFactory::make('binary');
echo $converter->encode(42); // "101010"
```

### Creating External Custom Converter

```php
use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\CustomConverterRegistry;

class MyCustomConverter implements IDConverterInterface
{
    public function encode(int $number): string
    {
        return 'X' . strtoupper(dechex($number));
    }

    public function decode(string $encoded): int
    {
        return hexdec(substr($encoded, 1));
    }
}

CustomConverterRegistry::register('my_custom', new MyCustomConverter());
$converter = ConverterFactory::make('my_custom');
```

### Registry Methods

```php
// Check if a converter is registered
CustomConverterRegistry::has('hex'); // true

// Get all registered converter names
CustomConverterRegistry::getRegisteredNames(); // ['hex', 'binary', ...]

// Unregister a converter
CustomConverterRegistry::unregister('hex');

// Clear all registered converters
CustomConverterRegistry::clear();
```

## API Reference

### `ConverterFactory::make(ConverterType $type): IDConverterInterface`

Create a new converter instance based on the specified type.

### `IDConverterInterface::encode(int $number): string`

Encode a non-negative integer into a string representation.

**Parameters:**
- `$number` - The integer to encode (must be >= 0)

**Returns:** The encoded string

**Throws:** `ConverterException` if the number is negative

### `IDConverterInterface::decode(string $encoded): int`

Decode a string representation back to an integer.

**Parameters:**
- `$encoded` - The encoded string to decode

**Returns:** The decoded integer

**Throws:** `ConverterException` if the string is empty or contains invalid characters

## Running Examples

```bash
# Basic usage examples
php examples/usage.php

# Custom strategy examples
php examples/custom-strategy.php

# DI and Octane safety examples
php examples/di-octane-safety.php
```

## Laravel Octane / Swoole Safety

### Important: Static Registry is NOT Safe

`CustomConverterRegistry` uses static storage which persists across requests in Swoole/Octane. This can cause data leakage between requests.

### Recommended: Use ConverterManager

```php
use Fatkulnurk\RadixConverter\ConverterManager;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

// Create manager instance (per-request or per-container)
$manager = new ConverterManager();

// Encode/decode
$encoded = $manager->encode(ConverterType::BASE62, 12345);
$decoded = $manager->decode(ConverterType::BASE62, $encoded);

// Register custom converters
$manager->registerCustom('hex', new HexStrategy());
```

### Dependency Injection Pattern

```php
class UrlShortenerService
{
    public function __construct(
        private ConverterManager $converterManager
    ) {}

    public function shorten(int $id): string
    {
        return $this->converterManager->encode(ConverterType::BASE62, $id);
    }
}
```

### Laravel Integration

The package includes a service provider for Laravel:

```php
// In config/app.php
'providers' => [
    Fatkulnurk\RadixConverter\Laravel\RadixConverterServiceProvider::class,
];

// Usage via dependency injection
class MyController extends Controller
{
    public function __construct(
        private ConverterManager $converterManager
    ) {}

    public function store(Request $request)
    {
        $code = $this->converterManager->encode(ConverterType::BASE62, $id);
    }
}
```

### Memory Management

For long-running processes, clear the cache periodically:

```php
// Clear cached converters (custom converters preserved)
$manager->clearCache();

// Clear all converters
$manager->clearAll();
```

## License

MIT License - see [LICENSE](LICENSE) for details.
