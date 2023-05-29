<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use UnexpectedValueException;

/**
 * @implements ConverterInterface<string, int>
 */
class StringToIntConverter implements ConverterInterface
{
    public function convert(string $input): int
    {
        $int = (int) $input;
        if ((string) $int !== $input) {
            throw new UnexpectedValueException(
                sprintf(
                    'Value "%s" can not be converted to int as it is not an integer',
                    $input
                )
            );
        }
        return $int; // TODO
    }
}