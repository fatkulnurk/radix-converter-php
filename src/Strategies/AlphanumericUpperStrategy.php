<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;


final readonly class AlphanumericUpperStrategy extends AbstractBaseConverter
{
    private const string CHARSET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected function getCharset(): string { return self::CHARSET; }
}