# Radix Converter PHP

A type-safe library to convert numbers into short strings and back. Perfect for URL shorteners, unique IDs, and compact representations.

[Packagist](https://packagist.org/packages/fatkulnurk/radix-converter)

---

## Table of Contents

- [What Does This Do?](#what-does-this-do)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Built-in Converters](#built-in-converters)
  - [Base62 Strategy](#1-base62-strategy-most-compact)
  - [Alphanumeric Upper Strategy](#2-alphanumeric-upper-strategy)
  - [Alphanumeric Lower Strategy](#3-alphanumeric-lower-strategy)
  - [Alpha Only Strategy](#4-alpha-only-strategy)
  - [Hex Strategy (Custom)](#5-hex-strategy-custom)
- [Common Use Cases](#common-use-cases)
  - [URL Shortener](#1-url-shortener)
  - [Different Converter Types](#2-different-converter-types)
  - [Error Handling](#3-error-handling)
- [Custom Converters](#custom-converters)
- [For Laravel Users](#for-laravel-users)
- [More Examples](#more-examples)
- [CI/CD](#cicd)
- [License](#license)

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

## Built-in Converters

The library comes with 5 built-in converters:

| Name | Characters Used | Base | Example Output |
|------|-----------------|------|----------------|
| BASE62 | Numbers + lowercase + uppercase | 62 | `3d7` |
| ALPHA_NUMERIC_UPPER | Numbers + uppercase letters | 36 | `RS` |
| ALPHA_NUMERIC_LOWER | Numbers + lowercase letters | 36 | `rs` |
| ALPHA_ONLY | Letters only (no numbers) | 52 | `cU` |
| HEX | Numbers + a-f | 16 | `ff` |

### How Each Strategy Works

All converters extend `AbstractBaseConverter` which uses the **radix (base-N) algorithm**:

**Encoding** (number → string):
1. Divide the number by the base
2. Use the remainder to pick a character from the charset
3. Repeat with the quotient until it becomes 0
4. Read the result from last to first

**Decoding** (string → number):
1. Start with 0
2. For each character, multiply result by base and add the character's position value
3. Return the final number

#### 1. Base62 Strategy (Most Compact)

- **Charset**: `0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ` (62 chars)
- **Base**: 62
- **Best for**: URL shorteners, compact unique IDs

```php
$converter = ConverterFactory::make(ConverterType::BASE62);
$converter->encode(12345); // "3d7"
$converter->decode("3d7"); // 12345
```

**Example calculation for `12345`**:
- 12345 ÷ 62 = 199 remainder **7** → charset[7] = "7"
- 199 ÷ 62 = 3 remainder **13** → charset[13] = "d"
- 3 ÷ 62 = 0 remainder **3** → charset[3] = "3"
- Result: **"3d7"**

#### 2. Alphanumeric Upper Strategy

- **Charset**: `0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ` (36 chars)
- **Base**: 36
- **Best for**: Case-insensitive identifiers, easier to read

```php
$converter = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_UPPER);
$converter->encode(1000); // "RS"
$converter->decode("RS"); // 1000
```

#### 3. Alphanumeric Lower Strategy

- **Charset**: `0123456789abcdefghijklmnopqrstuvwxyz` (36 chars)
- **Base**: 36
- **Best for**: Easy typing, lowercase-only systems

```php
$converter = ConverterFactory::make(ConverterType::ALPHA_NUMERIC_LOWER);
$converter->encode(1000); // "rs"
$converter->decode("rs"); // 1000
```

#### 4. Alpha Only Strategy

- **Charset**: `abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ` (52 chars)
- **Base**: 52
- **Best for**: When numbers must be excluded from identifiers

```php
$converter = ConverterFactory::make(ConverterType::ALPHA_ONLY);
$converter->encode(100); // "cU"
$converter->decode("cU"); // 100
```

#### 5. Hex Strategy (Custom)

- **Charset**: `0123456789abcdef` (16 chars)
- **Base**: 16
- **Best for**: Standard hexadecimal conversion

```php
use Fatkulnurk\RadixConverter\CustomConverterRegistry;

CustomConverterRegistry::register('hex', new \Fatkulnurk\RadixConverter\Strategies\HexStrategy());
$converter = CustomConverterRegistry::get('hex');
$converter->encode(255); // "ff"
$converter->decode("ff"); // 255
```

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

## Custom Converters

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

## CI/CD

This project uses GitHub Actions for continuous integration. The workflow is configured in `.github/workflows/ci.yml`.

### How It Works

The CI pipeline runs automatically on:
- Every push to `main` or `master` branches
- Every pull request targeting `main` or `master` branches

### Workflow Steps

| Step | Description |
|------|-------------|
| **Checkout** | Clones your code from GitHub |
| **Setup PHP** | Installs PHP 8.5 with required extensions |
| **Install Dependencies** | Runs `composer install` to get all packages |
| **Run Tests** | Executes PHPUnit tests via `composer test` |

### Running Tests Locally

Before pushing your changes, run tests locally:

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test:coverage
```

### Customizing the Workflow

To test on multiple PHP versions, edit `.github/workflows/ci.yml`:

```yaml
strategy:
  matrix:
    php: ['8.5', '8.6']  # Add more versions here
```

## License

MIT License - free to use in your projects.
