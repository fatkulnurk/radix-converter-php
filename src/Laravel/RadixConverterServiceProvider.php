<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * Laravel Service Provider for Radix Converter.
 *
 * Registers the ConverterManager as a singleton in the Laravel container.
 * This ensures proper dependency injection and memory safety in Octane.
 *
 * @example
 * // In config/app.php providers array (auto-discovered in Laravel 11+)
 * Fatkulnurk\RadixConverter\Laravel\RadixConverterServiceProvider::class,
 *
 * // Usage in a service
 * class MyService {
 *     public function __construct(
 *         private ConverterManager $converterManager
 *     ) {}
 * }
 */
class RadixConverterServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ConverterManager::class, function (Container $app): ConverterManager {
            $config = $app->get('config', []);
            $customConverters = $config->get('radix_converter.custom_converters', []);

            return new ConverterManager($customConverters);
        });

        $this->app->alias(ConverterManager::class, 'radix.converter');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/radix_converter.php' => config_path('radix_converter.php'),
        ], 'radix-converter-config');
    }
}
