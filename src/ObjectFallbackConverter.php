<?php
namespace Apie\TypeConverter;

use ReflectionNamedType;
use ReflectionType;

/**
 * @extends ConverterInterface<object, object>
 */
interface ObjectFallbackConverter extends ConverterInterface {
    public function convert(object $input, ReflectionNamedType $wantedType, TypeConverter $typeConverter): object;
}
