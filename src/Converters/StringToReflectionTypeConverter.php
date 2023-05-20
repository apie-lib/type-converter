<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

/**
 * @implements ConverterInterface<string, ReflectionType>
 */
class StringToReflectionTypeConverter implements ConverterInterface
{
    public function convert(string $input): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($input);
    }
}