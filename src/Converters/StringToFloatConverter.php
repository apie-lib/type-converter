<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use UnexpectedValueException;

/**
 * @implements ConverterInterface<string, float>
 */
class StringToFloatConverter implements ConverterInterface
{
    public function convert(string $input): float
    {
        $input = trim($input);
        if (!preg_match('/^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/', $input)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Value "%s" can not be converted to float as it is not a valid floating-point number',
                    $input
                )
            );
        }

        return (float) $input;
    }
}