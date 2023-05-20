<?php
namespace Apie\TypeConverter\Exceptions;

use LogicException;
use ReflectionType;
use Throwable;

class CanNotConvertObjectPropertyException extends LogicException
{
    public function __construct(
        mixed $data,
        string $propertyName,
        ReflectionType $wantedType,
        ?Throwable $previous = null
    ) {
        parent::__construct('I can not find a converter for property "' . get_debug_type($data) . '::$' . $propertyName . '" to "' . $wantedType . '"', 0, $previous);
    }
}