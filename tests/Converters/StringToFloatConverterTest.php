<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\Converters\StringToFloatConverter;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class StringToFloatConverterTest extends TestCase
{
    public function testConvertValidFloat()
    {
        $converter = new StringToFloatConverter();

        $input = '3.14';
        $result = $converter->convert($input);

        $this->assertEquals(3.14, $result);
    }

    public function testConvertInvalidFloat()
    {
        $this->expectException(UnexpectedValueException::class);

        $converter = new StringToFloatConverter();

        $input = 'invalid_float';
        $converter->convert($input);
    }
}