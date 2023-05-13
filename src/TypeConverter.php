<?php
namespace Apie\TypeConverter;

use Apie\TypeConverter\Converters\ReflectionPropertyToStringConverter;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\Utils\ConverterUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;
use ReflectionType;

final class TypeConverter {
    private ReflectionPropertyToStringConverter $cacheConverter;
    private array $converters = [];
    private array $inputMapping = [];
    public function __construct(ConverterInterface... $converters)
    {
        $this->cacheConverter = new ReflectionPropertyToStringConverter();
        foreach ($converters as $converter) {
            $input = ConverterUtil::getInput($converter);
            $output = ConverterUtil::getOutput($converter);
            $inputCacheKey = $this->cacheConverter->convert($input);
            $outputCacheKey = $this->cacheConverter->convert($output);
            $cacheKey = $inputCacheKey . ',' . $outputCacheKey;
            $this->inputMapping[$inputCacheKey][] = $converter;
            $this->converters[$cacheKey] = $converter;
        }
    }

    /**
     * @return ConverterInterface[]
     */
    private function getConvertersForData(mixed $data): array
    {
        $key = get_debug_type($data);
        if (!isset($this->inputMapping[$key])) {
            $this->inputMapping[$key] = [];
            foreach ($this->converters as $converter) {
                $input = ConverterUtil::getInput($converter);
                if ($input === null || ReflectionTypeUtil::isApplicable($data, $input)) {
                    $this->inputMapping[$key][] = $converter;
                }
            }
        }

        return $this->inputMapping[$key];
    }

    public function convertTo(mixed $data, ReflectionType $wantedType): mixed
    {
        $cacheKey = get_debug_type($data)
            . ','
            . $this->cacheConverter->convert($wantedType);
        if (isset($this->converters[$cacheKey])) {
            return $this->converters[$cacheKey]->convert($data);
        }
        $bestConverter = null;
        $score = null;
        $type = ReflectionTypeFactory::createReflectionType(get_debug_type($data));
        foreach ($this->getConvertersForData($data) as $converter) {
           
            $rating = ReflectionTypeUtil::rateAccuracy($wantedType, $type);
            if ($rating !== null)  {
                if ($score === null || $score > $rating) {
                    $score = $rating;
                    $bestConverter = $converter;
                }
            }
        }
        if ($bestConverter) {
            return $bestConverter->convert($data);
        }
        throw new CanNotConvertObjectException($data, $wantedType);
    }
}