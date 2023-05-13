<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Converters\NumberToStringConverter;
use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use PHPUnit\Framework\TestCase;

class TypeConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_pick_the_right_converter()
    {
        $testItem = new TypeConverter(new StringToIntConverter());
        $this->assertSame(
            12,
            $testItem->convertTo('12', ReflectionTypeFactory::createReflectionType('int'))
        );
    }

    /**
     * @test
     */
    public function it_prioritizes_more_accurate_typehints()
    {
        $testItem = new TypeConverter(
            new class implements ConverterInterface {
                public function convert(int|float $input): string {
                    return 'HI';
                }
            },
            new class implements ConverterInterface {
                public function convert(int $input): string {
                    return 'Hello';
                }
            }
        );
        $this->assertSame(
            'Hello',
            $testItem->convertTo(12, ReflectionTypeFactory::createReflectionType('string'))
        );
    }

    /**
     * @test
     */
    public function it_throws_error_if_the_right_converter_can_not_be_found()
    {
        $this->expectException(CanNotConvertObjectException::class);
        $testItem = new TypeConverter(new NumberToStringConverter());
        $testItem->convertTo($this, ReflectionTypeFactory::createReflectionType('int'));
    }
}