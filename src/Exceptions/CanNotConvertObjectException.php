<?php
namespace Apie\TypeConverter\Exceptions;

use LogicException;
use ReflectionType;
use Throwable;

class CanNotConvertObjectException extends LogicException
{
    public function __construct(
        mixed $data,
        ReflectionType $wantedType,
        ?Throwable $previous = null
    ) {
        parent::__construct('I can not find a converter from "' . get_debug_type($data) . '" to "' . $wantedType . '"', 0, $previous);
    }
}