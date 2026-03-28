<?php
declare(strict_types=1);
namespace Fatkulnurk\RadixConverter\Strategies;

final readonly class Base62Strategy extends AbstractBaseConverter
{
    private const string CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected function getCharset(): string { return self::CHARSET; }
}