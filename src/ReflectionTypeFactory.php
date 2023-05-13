<?php
namespace Apie\TypeConverter;

use LogicException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;
use RuntimeException;

/**
 * This code is evil.
 */
final class ReflectionTypeFactory
{
    /**
     * @var array<string, ReflectionType>
     */
    private static array $alreadyCreated = [];

    /**
     * @codeCoverageIgnore
     */
    private static function dummy(): string|false|null
    {
        return null;
    }

    private static function createNullType(): ReflectionType
    {
        $refl = new ReflectionMethod(__CLASS__, 'dummy');
        return $refl->getReturnType()->getTypes()[2];
    }

    private static function createFalseType(): ReflectionType
    {
        $refl = new ReflectionMethod(__CLASS__, 'dummy');
        return $refl->getReturnType()->getTypes()[1];
    }

    private static function createTrueType(): ReflectionType
    {
        if (PHP_VERSION_ID < 80200) {
            throw new LogicException('true typehint not supported in PHP < 8.2');
        }
        $fakeClass = eval(
            'return new class { public function method(): string|true {} };'
        );
        $refl = new ReflectionClass($fakeClass);
        return $refl->getMethod('method')->getReturnType()->getTypes()[1];
    }

    public static function createReflectionType(string $typehint): ReflectionType
    {
        if (strpos($typehint, ';') !== false || strpos($typehint, '/') !== false) {
            throw new RuntimeException('Are you trying to exploit this evil method?');
        }
        if (!isset(self::$alreadyCreated[$typehint])) {
            if ($typehint === 'null') {
                self::$alreadyCreated[$typehint] = self::createNullType();
            } elseif ($typehint === 'false') {
                self::$alreadyCreated[$typehint] = self::createFalseType();
            } elseif ($typehint === 'true') {
                self::$alreadyCreated[$typehint] = self::createTrueType();
            } else {
                $fakeClass = eval(
                    'return new class { public function method(): ' . $typehint . '{} };'
                );
                $refl = new ReflectionClass($fakeClass);
                self::$alreadyCreated[$typehint] = $refl->getMethod('method')->getReturnType();
            }
        }
        return self::$alreadyCreated[$typehint];
    }
}
