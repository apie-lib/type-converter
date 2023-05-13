<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;

/**
 * @implements ConverterInterface<string, int>
 */
class StringToIntConverter implements ConverterInterface
{
    public function convert(string $input): int
    {
        return (int) $input; // TODO
    }
}