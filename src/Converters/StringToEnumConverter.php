<?php

namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use Apie\TypeConverter\Utils\ConverterUtil;
use Apie\TypeConverter\Utils\PropertyIterateUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;

class StringToEnumConverter implements ConverterInterface
{
    public function convert(string $input, \ReflectionNamedType $wantedType, TypeConverter $typeConverter): \UnitEnum
    {
        $class = ReflectionTypeUtil::toClass($wantedType);
        assert ($class !== null);
        $className = $class->name;
        if ($result = $className::tryFrom($input)) {
            return $result;
        }
        foreach ($className::cases() as $case) {
            if ($input === $case->name || $input === (string) $case->value) {
                return $case;
            }
        }

        throw new CanNotConvertObjectException(
            $input,
            $wantedType
        );
    }
}