<?php

declare(strict_types=1);

namespace Fatkulnurk\RadixConverter\Strategies;

use Fatkulnurk\RadixConverter\Contracts\IDConverterInterface;
use Fatkulnurk\RadixConverter\Exceptions\ConverterException;

abstract readonly class AbstractBaseConverter implements IDConverterInterface
{
    protected readonly string $charset;
    protected readonly int $base;

    public function __construct()
    {
        $this->charset = $this->getCharset();
        $this->base = \strlen($this->charset);
    }

    abstract protected function getCharset(): string;

    /**
     * @throws ConverterException
     */
    #[\Override]
    public function encode(int $number): string
    {
        if ($number < 0) {
            throw new ConverterException('Input number must be positive');
        }

        if ($number === 0) {
            return $this->charset[0];
        }

        $result = '';
        while ($number > 0) {
            $remainder = $number % $this->base;
            $result = $this->charset[$remainder] . $result;
            $number = \intdiv($number, $this->base);
        }

        return $result;
    }

    /**
     * @throws ConverterException
     */
    #[\Override]
    public function decode(string $encoded): int
    {
        if ($encoded === '') {
            throw new ConverterException('Encoded value cannot be empty');
        }

        $result = 0;
        $length = \strlen($encoded);

        for ($i = 0; $i < $length; $i++) {
            $char = $encoded[$i];
            $position = \strpos($this->charset, $char);

            if ($position === false) {
                throw new ConverterException("Invalid character found: {$char}");
            }

            $result = ($result * $this->base) + $position;
        }

        return $result;
    }
}