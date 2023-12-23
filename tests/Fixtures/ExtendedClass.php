<?php
namespace Apie\Tests\TypeConverter\Fixtures;

final class ExtendedClass extends BaseClass
{
    public function setNumber(int $number) {
        $this->number = $number;
    }
}