<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Converters\IntToStringConverter;
use Apie\TypeConverter\Converters\NumberToStringConverter;
use Apie\TypeConverter\Converters\ObjectToObjectConverter;
use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ObjectFallbackConverter;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use PHPUnit\Framework\TestCase;

class TypeConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_pick_the_right_converter_with_caching()
    {
        $testItem = new TypeConverter(new ObjectToObjectConverter(), new StringToIntConverter());
        $this->assertSame(
            12,
            $testItem->convertTo('12', 'int')
        );
    }

    /**
     * @test
     */
    public function it_can_pick_the_right_converter_without_caching()
    {
        $testItem = new TypeConverter(
            new ObjectToObjectConverter(),
            new class implements ConverterInterface {
                public function convert(int|float $input): string {
                    return 'HI';
                }
            }
        );
        $this->assertSame(
            'HI',
            $testItem->convertTo(12, 'string')
        );
    }

    /**
     * @test
     */
    public function it_prioritizes_more_accurate_typehints()
    {
        $testItem = new TypeConverter(
            new ObjectToObjectConverter(),
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
            $testItem->convertTo(12, 'string')
        );
    }

    /**
     * @test
     */
    public function it_can_have_a_union_type_as_wanted_type()
    {
        $testItem = new TypeConverter(
            new ObjectToObjectConverter(),
            new class implements ConverterInterface {
                public function convert(int $input): int {
                    return 42;
                }
            },
            new IntToStringConverter(),
        );
        $this->assertSame(
            42,
            $testItem->convertTo(12, 'string|int')
        );
    }

    /**
     * @test
     */
    public function it_throws_error_if_the_right_converter_can_not_be_found()
    {
        $testItem = new TypeConverter(new ObjectToObjectConverter(), new IntToStringConverter());
        $this->expectException(CanNotConvertObjectException::class);
        $testItem->convertTo(12, 'int');
    }
}