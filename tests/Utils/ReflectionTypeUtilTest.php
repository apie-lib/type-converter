<?php
namespace Apie\Tests\TypeConverter\Utils;

use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\Utils\ConverterUtil;
use Apie\TypeConverter\Utils\ReflectionTypeUtil;
use PHPUnit\Framework\Reorderable;
use PHPUnit\Framework\SelfDescribing;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionType;
use stdClass;

class ReflectionTypeUtilTest extends TestCase
{
    /**
     * @dataProvider toClassProvider
     */
    public function testToClass(?string $expected, ReflectionType $type)
    {
        $this->assertEquals($expected ? new ReflectionClass($expected) : null, ReflectionTypeUtil::toClass($type));
    }

    public function toClassProvider()
    {
        yield [null, ReflectionTypeFactory::createReflectionType('string')];
        yield [stdClass::class, ReflectionTypeFactory::createReflectionType('stdClass')];
        yield [null, ReflectionTypeFactory::createReflectionType(__CLASS__ . '|stdClass')];
        yield [__CLASS__, ReflectionTypeFactory::createReflectionType(__CLASS__)];
    }

    /**
     * @dataProvider isApplicableProvider
     */
    public function testIsApplicable(bool $expected, mixed $input, string $type)
    {
        $this->assertEquals($expected, ReflectionTypeUtil::isApplicable($input, ReflectionTypeFactory::createReflectionType($type)));
    }

    public function isApplicableProvider()
    {
        $string = 'this is a string';
        $true = true;
        $false = false;
    
        yield [true, $string, 'string'];
        yield [false, null, 'string'];
        yield [false, $string, 'int'];
        yield [true, $string, 'string|int'];
        
        yield [true, $string, '?string'];
        yield [true, null, '?string'];

        yield [true, $true, 'bool'];
        yield [false, $string, 'bool'];
        yield [true, $false, 'bool'];
        yield [true, $false, 'false'];
        yield [false, $true, 'false'];
        if (PHP_VERSION_ID >= 80200) {
            yield [true, $true, 'true'];
            yield [false, $false, 'true'];
        }

        yield [false, $string, 'object'];
        yield [false, $true, 'object'];
        yield [false, null, 'object'];
        yield [true, new stdClass, 'object'];
        yield [true, $this, 'object'];
        yield [true, $this, __CLASS__];
        yield [true, $this, TestCase::class];
        yield [true, $this, Reorderable::class . '&' .  SelfDescribing::class];

    }

    /**
     * @dataProvider rateAccuracyProvider
     */
    public function testRateAccuracy(?int $expected, string $wantedType, string $argument)
    {
        $this->assertEquals($expected, ReflectionTypeUtil::rateAccuracy(
            ReflectionTypeFactory::createReflectionType($wantedType),
            ReflectionTypeFactory::createReflectionType($argument)
        ));
    }

    public function rateAccuracyProvider()
    {
        yield [1000, 'string', 'string'];
        yield [900, 'string', '?string'];
        yield [400, 'string', 'mixed'];
        yield [1000, 'null', 'null'];
        yield [400, 'null', '?string'];
        yield [500, 'string|int', 'string'];
        yield [500, 'string|int|bool', 'string'];
        $intersection = Reorderable::class . '&' .  SelfDescribing::class;
        yield [500, __CLASS__, $intersection];
        yield [null, 'string', $intersection];
        yield [null, 'string|int', $intersection];
        yield [null, $intersection, $intersection]; //TODO
        yield [null, 'string', 'string|int'];
        yield [500, 'string|int|null', 'null'];
        yield [null, 'null', 'string|int|null'];
    }
}