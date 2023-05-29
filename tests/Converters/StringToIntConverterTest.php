<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\IntToStringConverter;
use Apie\TypeConverter\Converters\StringToIntConverter;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class StringToIntConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_convert_an_int_to_a_string()
    {
        $testItem = new StringToIntConverter();
        $this->assertEquals(42, $testItem->convert('42'));
        $this->expectException(UnexpectedValueException::class);
        $testItem->convert('12 this is not a number');
    }
}