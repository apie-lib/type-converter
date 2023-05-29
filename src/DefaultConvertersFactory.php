<?php
namespace Apie\TypeConverter;

use Apie\TypeConverter\Converters\FloatToStringConverter;
use Apie\TypeConverter\Converters\IntToStringConverter;
use Apie\TypeConverter\Converters\ReflectionPropertyToStringConverter;
use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\Converters\StringToReflectionTypeConverter;

final class DefaultConvertersFactory {
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return ConverterInterface<mixed, mixed>[]
     */
    public static function create(ConverterInterface... $converters): array
    {
        return [
            new FloatToStringConverter(),
            new IntToStringConverter(),
            new StringToIntConverter(),
            new ReflectionPropertyToStringConverter(),
            new StringToReflectionTypeConverter(),
            ...$converters
        ];
    }
}