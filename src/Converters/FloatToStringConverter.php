<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;

/**
 * @implements ConverterInterface<float, string>
 */
class FloatToStringConverter implements ConverterInterface
{
    public function convert(float $input): string
    {
        return (string) $input;
    }
}