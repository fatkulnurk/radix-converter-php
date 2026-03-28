<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

final readonly class AlphaOnlyStrategy extends AbstractBaseConverter
{
    private const string CHARSET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected function getCharset(): string { return self::CHARSET; }
}