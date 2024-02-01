<?php
namespace Apie\TypeConverter\Exceptions;

use Throwable;

interface GetMultipleChainedExceptionInterface extends Throwable
{
    /**
     * @return array<string|int, Throwable>
     */
    public function getChainedExceptions(): array;
}