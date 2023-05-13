<?php
namespace Apie\TypeConverter\Exceptions;

use LogicException;
use ReflectionType;

class CanNotConvertObjectException extends LogicException
{
    public function __construct(
        mixed $data,
        ReflectionType $wantedType
    ) {
        parent::__construct('I can not find a converter from "' . get_debug_type($data) . '" to "' . $wantedType . '"');
    }
}