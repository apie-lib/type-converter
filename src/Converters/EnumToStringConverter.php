<?php

namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\TypeConverter;
use Apie\TypeConverter\Utils\PropertyIterateUtil;

class EnumToStringConverter implements ConverterInterface
{
    public function convert(\UnitEnum $input, \ReflectionNamedType $wantedType, TypeConverter $typeConverter): string
    {
        return $input->name;
    }
}