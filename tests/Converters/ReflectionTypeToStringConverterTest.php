<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\IntToStringConverter;
use Apie\TypeConverter\Converters\ReflectionTypeToStringConverter;
use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionType;

class ReflectionTypeToStringConverterTest extends TestCase
{
    /**
     * @dataProvider provideTypes
     * @test
     */
    public function it_can_convert_a_float_to_a_string(string $expected, ReflectionType $type)
    {
        $testItem = new ReflectionTypeToStringConverter();
        $this->assertEquals(
            $expected,
            $testItem->convert($type)
        );
    }

    public static function provideTypes(): Generator
    {
        yield ['string', ReflectionTypeFactory::createReflectionType('string')];
        yield ['(string|int)', ReflectionTypeFactory::createReflectionType('string|int')];
        yield [
            '(IteratorAggregate&JsonSerializable)',
            ReflectionTypeFactory::createReflectionType('IteratorAggregate&JsonSerializable')
        ];
       yield [
            '((IteratorAggregate&JsonSerializable)|float)',
            ReflectionTypeFactory::createReflectionType('(IteratorAggregate&JsonSerializable)|float')
        ];
    }
}