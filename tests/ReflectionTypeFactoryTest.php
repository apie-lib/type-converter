<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use PHPUnit\Framework\TestCase;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;

class ReflectionTypeFactoryTest extends TestCase
{
    /**
     * @dataProvider createProvider
     * @test
     */
    public function it_can_create_reflection_types_from_string(string $expectedType, string $input)
    {
        $this->assertInstanceof($expectedType, ReflectionTypeFactory::createReflectionType($input));
    }

    public function createProvider(): Generator
    {

        yield [ReflectionNamedType::class, 'string'];
        yield [ReflectionNamedType::class, 'object'];
        yield [ReflectionNamedType::class, 'false'];
        yield [ReflectionNamedType::class, 'null'];
        yield [ReflectionIntersectionType::class, 'FirstInterface&SecondInterface'];
        yield [ReflectionUnionType::class, 'string|int'];

    }
}