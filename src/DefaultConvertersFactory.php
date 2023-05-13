<?php
namespace Apie\TypeConverter;

use Apie\TypeConverter\Converters\NumberToStringConverter;
use Apie\TypeConverter\Converters\ReflectionPropertyToStringConverter;
use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\Converters\StringToReflectionTypeConverter;

final class DefaultConvertersFactory {
    private function __construct()
    {
    }

    /**
     * @return ConverterInterface<mixed, mixed>[]
     */
    public function create(): array
    {
        return [
            new NumberToStringConverter(),
            new StringToIntConverter(),
            new ReflectionPropertyToStringConverter(),
            new StringToReflectionTypeConverter(),

        ];
    }
}