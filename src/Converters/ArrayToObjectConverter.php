<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectPropertyException;
use Apie\TypeConverter\ObjectFallbackConverter;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use Apie\TypeConverter\Utils\PropertyIterateUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;
use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use UnexpectedValueException;

/**
 * @implements ConverterInterface<array<string, mixed>, object>
 */
final class ArrayToObjectConverter implements ConverterInterface
{
    private readonly PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor =  PropertyAccess::createPropertyAccessor();
    }

    public function convert(array $array, ReflectionNamedType $wantedType, TypeConverter $typeConverter): object
    {
        $refl = new ReflectionClass($wantedType->getName());
        $constructorArguments = [];
        $constructor = $refl->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $propertyName = $parameter->name;
                $propertyValue = $array[$propertyName] ?? null;
                if (array_key_exists($propertyName, $array)) {
                    if (ReflectionTypeUtil::isApplicable($propertyValue, $parameter->getType())) {
                        $constructorArguments[] = $propertyValue;
                    } else {
                        $constructorArguments[] = $typeConverter->convertTo($propertyValue, $parameter->getType());
                    }
                    unset($array[$propertyName]);
                } else {
                    if ($parameter->hasDefaultValue()) {
                        $constructorArguments[] = $parameter->getDefaultValue();
                    } else {
                        throw new UnexpectedValueException('Property "' . $propertyName . '" is not found!');
                    }
                }
            }
        }
        $instance = $refl->newInstanceArgs($constructorArguments);
        $target = PropertyIterateUtil::getWriteProperties($refl);
        foreach ($target as $propertyName => $targetType) {
            if (!array_key_exists($propertyName, $array)) {
                continue;
            }
            $propertyValue = $array[$propertyName] ?? null;
            try {
                if (!ReflectionTypeUtil::isApplicable($propertyValue, $targetType)) {
                    $propertyValue = $typeConverter->convertTo($propertyValue, $targetType);
                }
                
                $this->propertyAccessor->setValue($instance, $propertyName, $propertyValue);
            } catch (Exception $error) {
                throw new CanNotConvertObjectPropertyException(
                    $instance,
                    $propertyName,
                    $targetType,
                    $error
                );
            }
        }

        return $instance;
    }
}