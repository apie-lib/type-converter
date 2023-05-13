<?php
namespace Apie\Tests\TypeConverter\Utils;

use Apie\TypeConverter\Converters\StringToIntConverter;
use Apie\TypeConverter\Utils\ConverterUtil;
use PHPUnit\Framework\TestCase;

class ConverterUtilTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_functionality_to_read_convert_methods()
    {
        $this->assertEquals('string', ConverterUtil::getInput(new StringToIntConverter()));
        $this->assertEquals('int', ConverterUtil::getOutput(new StringToIntConverter()));
    }
}