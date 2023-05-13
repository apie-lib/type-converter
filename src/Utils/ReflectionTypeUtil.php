<?php
namespace Apie\TypeConverter\Utils;

use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class ReflectionTypeUtil {
    private function __construct()
    {
    }

    public static function toClass(ReflectionType $type): ?ReflectionClass
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            return new ReflectionClass($type->getName());
        }
        return null;
    }

    public static function isApplicable(mixed $input, ReflectionType $type): bool
    {
        if ($input === null && $type->allowsNull()) {
            return true;
        }
        if ($type instanceof ReflectionNamedType) {
            if ($type->isBuiltin()) {
                $name = $type->getName();
                return match ($name) {
                    'false' => $input === false,
                    'true' => $input === true,
                    'object' => is_object($input),
                    default => get_debug_type($input) === $name,
                };
            }
            return is_object($input) ? is_a(get_class($input), $type->getName(), true) : false;
        }
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $type) {
                if (self::isApplicable($input, $type)) {
                    return true;
                }
            }
            return false;
        }
        assert($type instanceof ReflectionIntersectionType);
        foreach ($type->getTypes() as $type) {
            if (!self::isApplicable($input, $type)) {
                return false;
            }
        }
        return true;
    }

    public static function rateAccuracy(ReflectionType $wantedType, ReflectionType $argument): ?int
    {
        if ($argument instanceof ReflectionNamedType) {
            return self::rateOnNamedType($wantedType, $argument);
        }
        if ($argument instanceof ReflectionIntersectionType) {
            return self::rateOnIntersectionType($wantedType, $argument);
        }
        assert($argument instanceof ReflectionUnionType);
        return self::rateOnUnionType($wantedType, $argument);
    }

    private static function rateOnUnionType(ReflectionType $wantedType, ReflectionUnionType $argument): ?int
    {
        if ($wantedType instanceof ReflectionNamedType) {
            $class = self::toClass($wantedType);
            if ($class) {
                $type = ReflectionTypeFactory::createReflectionType($wantedType->getName());
                $types = $argument->getTypes();
                $total = null;
                foreach ($types as $argumentSubType) {
                    $rating = self::rateAccuracy($type, $argumentSubType);
                    if ($rating !== null) {
                        $total = $total === null ? $rating : min($rating, $total);
                    }
                }
                return $total === null ? null : min(500, $total);
            }
            return null;
        }
        // TODO: $wantedType instanceof ReflectionUnionType
        // TODO: $wantedType instanceof ReflectionIntersectionType
        return null;
    }

    private static function rateOnIntersectionType(ReflectionType $wantedType, ReflectionIntersectionType $argument): ?int
    {
        if ($wantedType instanceof ReflectionNamedType) {
            $class = self::toClass($wantedType);
            if ($class) {
                $type = ReflectionTypeFactory::createReflectionType($wantedType->getName());
                $types = $argument->getTypes();
                $total = 0;
                foreach ($types as $argumentSubType) {
                    $rating = self::rateAccuracy($type, $argumentSubType);
                    if ($rating === null) {
                        return null;
                    }
                    $total += $rating;
                }

                return min(500, $total / count($types));
            }
            return null;
        }
        // TODO: $wantedType instanceof ReflectionUnionType
        // TODO: $wantedType instanceof ReflectionIntersectionType
        return null;
    }

    private static function rateOnNamedType(ReflectionType $wantedType, ReflectionNamedType $argument): ?int
    {
        if ($wantedType instanceof ReflectionNamedType) {
            if ($argument->getName() === $wantedType->getName()) {
                return $argument->allowsNull() === $wantedType->allowsNull() ? 1000 : 900;
            }
            if (is_a($wantedType->getName(), $argument->getName(), true)) {
                return $argument->allowsNull() === $wantedType->allowsNull() ? 900 : 800;
            }

            return null;
        }
        if ($wantedType instanceof ReflectionIntersectionType) {
            $types = $wantedType->getTypes();
            $total = 0;
            foreach ($types as $wantedSubType) {
                $rating = self::rateAccuracy($wantedSubType, $argument);
                if ($rating === null) {
                    return null;
                }
                $total += $rating;
            }

            return min(500, $total / count($types));
        }
        assert($wantedType instanceof ReflectionUnionType);
        $types = $wantedType->getTypes();
        $total = null;
        foreach ($types as $wantedSubType) {
            $rating = self::rateAccuracy($wantedSubType, $argument);
            if ($rating !== null) {
                $total = $total === null ? $rating : min($rating, $total);
            }
        }
        return $total === null ? null : min(500, $total);
    }
}