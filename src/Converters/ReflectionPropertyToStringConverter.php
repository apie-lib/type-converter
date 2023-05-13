<?php
namespace Apie\TypeConverter\Converters;

use Apie\TypeConverter\ConverterInterface;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;

/**
 * @implements ConverterInterface<ReflectionType, string>
 */
class ReflectionPropertyToStringConverter implements ConverterInterface
{
    public function convert(ReflectionType $input): string
    {
        if ($input instanceof ReflectionNamedType) {
            return $input->getName();
        }
        $separator = '|';
        if ($input instanceof ReflectionIntersectionType) {
            $separator = '&';
        }
        return '('
            . implode(
                $separator,
                array_map(
                    function (ReflectionType $type) {
                        return $this->convert($type);
                    },
                    $input->getTypes()
                )
            )
            . ')';
    }
}