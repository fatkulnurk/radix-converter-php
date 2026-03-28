# Radix Converter PHP

A type-safe library to convert numbers into short strings and back. Perfect for URL shorteners, unique IDs, and compact representations.

[Packagist](https://packagist.org/packages/fatkulnurk/radix-converter)

---

## What Does This Do?

This library converts numbers like `12345` into short strings like `"3d7"` and back. This is useful for:

- Creating short URLs (like `bit.ly/3d7`)
- Generating compact unique IDs
- Making numbers easier to read and share

## Installation

Run this command in your terminal:

```bash
composer require fatkulnurk/radix-converter
```

## Requirements

- PHP 8.5 or higher

## Quick Start

Here's the simplest way to use it:

```php
<?php

require_once 'vendor/autoload.php';

use Fatkulnurk\RadixConverter\ConverterFactory;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

// Create a converter
$converter = ConverterFactory::make(ConverterType::BASE62);

// Convert number to string
$encoded = $converter->encode(12345);
echo $encoded; // Output: "3d7"

// Convert string back to number
$decoded = $converter->decode($encoded);
echo $decoded; // Output: 12345
```

## Available Converters

The library comes with 4 built-in converters:

| Name | Characters Used | Example Output |
|------|-----------------|----------------|
| BASE62 | Numbers + lowercase + uppercase | `3d7` |
| ALPHA_NUMERIC_UPPER | Numbers + uppercase letters | `RS` |
| ALPHA_NUMERIC_LOWER | Numbers + lowercase letters | `rs` |
| ALPHA_ONLY | Letters only (no numbers) | `cU` |

## Common Use Cases

### 1. URL Shortener

Convert database IDs into short codes for URLs:

```php
$converter = ConverterFactory::make(ConverterType::BASE62);

// Your database ID
$databaseId = 154832;

// Convert to short code
$shortCode = $converter->encode($databaseId);
// Result: "Ehi"

// Create short URL
$shortUrl = "https://yoursite.com/u/" . $shortCode;
// Result: "https://yoursite.com/u/Ehi"

// Later, get the original ID back
$originalId = $converter->decode($shortCode);
// Result: 154832
```

### 2. Different Converter Types

```php
// Base62 (most compact)
$base62 = ConverterFactory::make(ConverterType::BASE62);
echo $base62->encode(1000); // "g8"

// Uppercase only (easier to read)
$upper = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_UPPER);
echo $upper->encode(1000); // "RS"

// Lowercase only (easier to type)
$lower = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);
echo $lower->encode(1000); // "rs"

// Letters only (no numbers)
$alpha = ConverterFactory::make(ConverterType::ALPHA_ONLY);
echo $alpha->encode(100); // "cU"
```

### 3. Error Handling

The library will throw errors if you try to:
- Encode negative numbers
- Decode empty strings
- Decode invalid characters

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

## Advanced: Custom Converters

You can create your own converter for any base system.

### Using the Built-in Hex Converter

```php
use Fatkulnurk\RadixConverter\CustomConverterRegistry;

// Register the hex converter
CustomConverterRegistry::register('hex', new \Fatkulnurk\RadixConverter\Strategies\HexStrategy());

// Use it
$converter = CustomConverterRegistry::get('hex');
echo $converter->encode(255); // "ff"
echo $converter->decode('ff'); // 255
```

### Creating Your Own Converter

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

## For Laravel Users

### Laravel Octane / Swoole Warning

If you use Laravel Octane or Swoole, do NOT use `CustomConverterRegistry` because it keeps data between requests, which can cause problems.

Instead, use `ConverterManager`:

```php
use Fatkulnurk\RadixConverter\ConverterManager;
use Fatkulnurk\RadixConverter\Enums\ConverterType;

// Create a manager
$manager = new ConverterManager();

// Use it
$encoded = $manager->encode(ConverterType::BASE62, 12345);
$decoded = $manager->decode(ConverterType::BASE62, $encoded);
```

### Using in Laravel Controllers

```php
class MyController extends Controller
{
    public function __construct(
        private ConverterManager $converterManager
    ) {}

    public function store(Request $request)
    {
        $code = $this->converterManager->encode(ConverterType::BASE62, 123);
        // ...
    }
}
```

## More Examples

The library includes example files you can run:

```bash
# Basic usage
php examples/usage.php

# Custom converters
php examples/custom-strategy.php

# Laravel Octane safety
php examples/di-octane-safety.php
```

## License

MIT License - free to use in your projects.
