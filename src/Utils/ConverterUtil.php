<?php
namespace Apie\TypeConverter\Utils;

use Apie\TypeConverter\ConverterInterface;
use ReflectionClass;
use ReflectionType;

final class ConverterUtil {
    private function __construct()
    {
    }

    public static function getInput(ConverterInterface $converter): ?ReflectionType
    {
        $method = (new ReflectionClass($converter))->getMethod('convert');
        $parameters = $method->getParameters();
        assert(!empty($parameters[0]));
        assert(1 === $method->getNumberOfRequiredParameters());
        return $parameters[0]->getType();
    }

    public static function getOutput(ConverterInterface $converter): ?ReflectionType
    {
        $method = (new ReflectionClass($converter))->getMethod('convert');
        return $method->getReturnType();
    }
}