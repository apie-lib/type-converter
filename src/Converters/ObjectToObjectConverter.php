<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\ObjectFallbackConverter;
use Apie\TypeConverter\TypeConverter;
use Apie\TypeConverter\Utils\PropertyIterateUtil;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use ReflectionClass;
use ReflectionNamedType;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;

/**
 * @implements ConverterInterface<object, object>
 */
final class ObjectToObjectConverter implements ObjectFallbackConverter
{
    private readonly PropertyAccessor $propertyAccessor;
    private readonly ArrayToObjectConverter $internal;

    public function __construct() {
        $this->internal = new ArrayToObjectConverter();
        $this->propertyAccessor =  PropertyAccess::createPropertyAccessor();
    }
    
    public function convert(object $input, ReflectionNamedType $wantedType, TypeConverter $typeConverter): object
    {
        $array = [];
        foreach (array_keys(PropertyIterateUtil::getReadProperties(new ReflectionClass($input))) as $propertyName) {
            try {
                $array[$propertyName] = $this->propertyAccessor->getValue($input, $propertyName);
            } catch (UninitializedPropertyException) {
            }
        }
        return $this->internal->convert($array, $wantedType, $typeConverter);
    }
}