<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\StringToReflectionTypeConverter;
use Apie\TypeConverter\ReflectionTypeFactory;
use PHPUnit\Framework\TestCase;

class StringToReflectionTypeConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_convert_a_string_to_reflection_type()
    {
        $testItem = new StringToReflectionTypeConverter();
        $this->assertEquals(ReflectionTypeFactory::createReflectionType('false'), $testItem->convert('false'));
    }
}