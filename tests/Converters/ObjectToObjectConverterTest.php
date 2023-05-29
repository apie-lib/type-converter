<?php
namespace Apie\Tests\TypeConverter;

use Apie\Tests\TypeConverter\Fixtures\DomainObject;
use Apie\Tests\TypeConverter\Fixtures\DtoExample;
use Apie\TypeConverter\Converters\ObjectToObjectConverter;
use Apie\TypeConverter\DefaultConvertersFactory;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use PHPUnit\Framework\TestCase;

class ObjectToObjectConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_convert_objects()
    {
        $fallback = new ObjectToObjectConverter();
        $testItem = new TypeConverter(
            $fallback,
            ...DefaultConvertersFactory::create()
        );
        $object = new DomainObject(
            'input',
            12,
            null
        );
        $actual = $testItem->convertTo($object, ReflectionTypeFactory::createReflectionType(DtoExample::class));
        $this->assertInstanceOf(DtoExample::class, $actual);
        $this->assertEquals('input', $actual->input);
        $this->assertEquals(12, $actual->number);
        $this->assertNull($actual->optional);
        $revert = $testItem->convertTo($actual, ReflectionTypeFactory::createReflectionType(DomainObject::class));
        $this->assertEquals($object, $revert);
    }

    /**
     * @test
     */
    public function it_throws_error_if_values_are_not_set()
    {
        $fallback = new ObjectToObjectConverter();
        $testItem = new TypeConverter(
            $fallback,
            ...DefaultConvertersFactory::create()
        );
        $object = new DtoExample();
        $this->expectException(CanNotConvertObjectException::class);
        $testItem->convertTo($object, DomainObject::class);
    }
}