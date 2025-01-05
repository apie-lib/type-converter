<?php
namespace Apie\TypeConverter\Utils;

use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class PropertyIterateUtil {
    /**
     * @codeCoverageIgnore
     */
    private function __construct() {
    }

    /**
     * @return array<string, ReflectionType>
     */
    public static function getReadProperties(ReflectionClass $class): array
    {
        $result = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (PHP_VERSION_ID >= 80400 && $property->isVirtual()) {
                $method = $property->getHook(\PropertyHookType::Get);
                if ($method === null) {
                    continue;
                }
                $result[$property->name] = $method->getReturnType() ?? ReflectionTypeFactory::createReflectionType('mixed');
            } else {
                $result[$property->name] = $property->getType() ?? ReflectionTypeFactory::createReflectionType('mixed');
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }
            if (str_starts_with($method->name, 'get') || str_starts_with($method->name, 'has')) {
                $result[lcfirst(substr($method->name, 3))] = $method->getReturnType();
            }
            if (str_starts_with($method->name, 'is')) {
                $result[lcfirst(substr($method->name, 2))] = $method->getReturnType();
            }
        }

        return $result;
    }

    /**
     * @return array<string, ReflectionType>
     */
    public static function getWriteProperties(ReflectionClass $class): array
    {
        $result = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly()) {
                continue;
            }
            $type = PHP_VERSION_ID >= 80400 ? $property->getSettableType() : $property->getType();
            if ((string) $type === 'never') {
                continue;
            }
            $result[$property->name] = $type ?? ReflectionTypeFactory::createReflectionType('mixed');
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }
            $arguments = $method->getParameters();
            if (str_starts_with($method->name, 'set') && !empty($arguments) && $method->getNumberOfRequiredParameters() === 1) {
                $result[lcfirst(substr($method->name, 3))] = reset($arguments)->getType() ?? ReflectionTypeFactory::createReflectionType('mixed');
            }
        }

        return $result;
    }
}