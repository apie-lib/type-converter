<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\IntToStringConverter;
use PHPUnit\Framework\TestCase;

class IntToStringConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_convert_an_int_to_a_string()
    {
        $testItem = new IntToStringConverter();
        $this->assertEquals("42", $testItem->convert(42));
    }
}