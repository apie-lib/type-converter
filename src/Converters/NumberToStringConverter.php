<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;

/**
 * @implements ConverterInterface<int|float, string>
 */
class NumberToStringConverter implements ConverterInterface
{
    public function convert(int|float $input): string
    {
        return (string) $input;
    }
}