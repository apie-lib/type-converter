<?php
namespace Apie\TypeConverter\Exceptions;

use LogicException;
use ReflectionUnionType;

final class CanNotConvertObjectToUnionException extends LogicException implements GetMultipleChainedExceptionInterface
{
    /**
     * @param array<string, Throwable> $errors
     */
    public function __construct(mixed $data, private array $errors, ReflectionUnionType $wantedType)
    {
        $messages = [];
        $previous = null;
        foreach ($errors as $error) {
            $messages[] = '"' . $error->getMessage() . '"';
            $previous = $error;
        }
        parent::__construct(
            'Can not find a converter for "' . get_debug_type($data) . '" to "' . $wantedType . '". I got these errors: ' . implode(', ', $messages),
            0,
            $previous
        );
    }

    public function getChainedExceptions(): array
    {
        return $this->errors;
    }
}