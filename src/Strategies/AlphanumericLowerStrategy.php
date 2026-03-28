<?php

namespace Fatkulnurk\RadixConverter\Strategies;

final readonly class AlphanumericLowerStrategy extends AbstractBaseConverter
{
    private const string CHARSET = '0123456789abcdefghijklmnopqrstuvwxyz';
    protected function getCharset(): string { return self::CHARSET; }
}