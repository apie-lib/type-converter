<?php
namespace Apie\Tests\TypeConverter\Fixtures;

abstract class BaseClass implements GetNumberInterface
{
    public function __construct(protected int $number)
    {
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}