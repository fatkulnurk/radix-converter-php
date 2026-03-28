<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Enums;

enum ConverterType
{
    case BASE62;
    case ALPHA_NUMERIC_UPPER;
    case ALPHA_NUMERIC_LOWER;
    case ALPHA_ONLY;
}
