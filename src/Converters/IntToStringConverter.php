<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;

/**
 * @implements ConverterInterface<int, string>
 */
class IntToStringConverter implements ConverterInterface
{
    public function convert(int $input): string
    {
        return (string) $input;
    }
}