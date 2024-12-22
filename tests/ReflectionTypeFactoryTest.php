<?php
namespace Apie\Tests\TypeConverter;

use Apie\TypeConverter\ReflectionTypeFactory;
use Generator;
use LogicException;
use PHPUnit\Framework\TestCase;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;

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

    public static function createProvider(): Generator
    {

        yield [ReflectionNamedType::class, 'string'];
        yield [ReflectionNamedType::class, 'object'];
        yield [ReflectionNamedType::class, 'false'];
        yield [ReflectionNamedType::class, 'null'];
        yield [ReflectionIntersectionType::class, 'FirstInterface&SecondInterface'];
        yield [ReflectionUnionType::class, 'string|int'];
        if (PHP_VERSION_ID > 80200) {
            yield [ReflectionNamedType::class, 'true'];
        }
    }

    /**
     * @test
     */
    public function it_prevents_simple_eval_exploits()
    {
        $this->expectException(RuntimeException::class);
        ReflectionTypeFactory::createReflectionType('true; private $exploit = true;');
    }
}