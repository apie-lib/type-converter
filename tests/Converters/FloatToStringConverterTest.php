<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\FloatToStringConverter;
use PHPUnit\Framework\TestCase;

class FloatToStringConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_convert_a_float_to_a_string()
    {
        $testItem = new FloatToStringConverter();
        $this->assertEquals("1.2", $testItem->convert(1.2));
    }
}