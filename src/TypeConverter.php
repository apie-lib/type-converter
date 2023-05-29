<?php
namespace Apie\TypeConverter;

use Apie\TypeConverter\Converters\ReflectionTypeToStringConverter;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\Utils\ConverterUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;
use ReflectionType;
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
        $bestConverter = null;
        $score = null;
        $type = ReflectionTypeFactory::createReflectionType(get_debug_type($data));
        foreach ($this->getConvertersForData($data) as $converter) {
           
            $rating = ReflectionTypeUtil::rateAccuracy(ConverterUtil::getOutput($converter), $wantedType);
            if ($rating !== null)  {
                if ($score === null || $score > $rating) {
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