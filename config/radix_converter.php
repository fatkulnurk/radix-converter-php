<?php

/**
 * Laravel configuration for Radix Converter.
 *
 * Publish this file with:
 * php artisan vendor:publish --provider="Fatkulnurk\RadixConverter\Laravel\RadixConverterServiceProvider" --tag="radix-converter-config"
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Custom Converters
    |--------------------------------------------------------------------------
    |
    | Define custom converters that will be registered automatically.
    | The key is the converter name, the value is the fully qualified class name.
    |
    */
    'custom_converters' => [
        // 'hex' => \Fatkulnurk\RadixConverter\Strategies\HexStrategy::class,
    ],
];
