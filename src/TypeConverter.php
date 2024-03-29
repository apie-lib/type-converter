<?php
namespace Apie\TypeConverter;

use Apie\TypeConverter\Converters\ReflectionTypeToStringConverter;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectToUnionException;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\Utils\ConverterUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;
use ReflectionType;
use ReflectionUnionType;
use Throwable;

final class TypeConverter {
    private ReflectionTypeToStringConverter $cacheConverter;
    private array $converters = [];
    private array $inputMapping = [];
    public function __construct(private readonly ObjectFallbackConverter $fallbackConverter, ConverterInterface... $converters)
    {
        $this->cacheConverter = new ReflectionTypeToStringConverter();
        foreach ($converters as $converter) {
            $input = ConverterUtil::getInput($converter);
            $output = ConverterUtil::getOutput($converter);
            $inputCacheKey = $this->cacheConverter->convert($input);
            $outputCacheKey = $this->cacheConverter->convert($output);
            $cacheKey = $inputCacheKey . ',' . $outputCacheKey;
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

    private function convertToUnion(mixed $data, ReflectionUnionType $wantedType): mixed
    {
        $todo = $wantedType->getTypes();
        $errors = [];
        // TODO: sort on priority (do float and int check before string)
        while (!empty($todo)) {
            $current = array_pop($todo);
            try {
                return $this->convertTo($data, $current);
            } catch (Throwable $throwable) {
                $errors[(string) $current] = $throwable;
            }
        }
        throw new CanNotConvertObjectToUnionException($data, $errors, $wantedType);
    }

    public function convertTo(mixed $data, ReflectionType|string $wantedType): mixed
    {
        if (is_string($wantedType)) {
            $wantedType = ReflectionTypeFactory::createReflectionType($wantedType);
        }
        $cacheKey = get_debug_type($data)
            . ','
            . $this->cacheConverter->convert($wantedType);
        if (isset($this->converters[$cacheKey])) {
            return $this->converters[$cacheKey]->convert($data, $wantedType, $this);
        }
        if ($wantedType instanceof ReflectionUnionType) {
            return $this->convertToUnion($data, $wantedType);
        }
        $bestConverter = null;
        $score = null;
        foreach ($this->getConvertersForData($data) as $converter) {
            $rating = ReflectionTypeUtil::rateAccuracy($wantedType, ConverterUtil::getOutput($converter));
            if ($rating !== null)  {
                if ($score === null || $score < $rating) {
                    $score = $rating;
                    $bestConverter = $converter;
                }
            }
        }
        if ($bestConverter) {
            return $bestConverter->convert($data, $wantedType, $this);
        }
        if (is_object($data)) {
            try {
                return $this->fallbackConverter->convert($data, $wantedType, $this);
            } catch (Throwable $error) {
                throw new CanNotConvertObjectException($data, $wantedType, $error);
            }
        }
        throw new CanNotConvertObjectException($data, $wantedType);
    }
}