<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Enums;

enum ConverterType: string
{
    case BASE62 = 'base62';
    case ALPHA_NUMERIC_UPPER = 'alphanumeric_upper';
    case ALPHA_NUMERIC_LOWER = 'alphanumeric_lower';
    case ALPHA_ONLY = 'alpha_only';
}
