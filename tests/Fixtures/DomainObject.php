<?php
namespace Apie\Tests\TypeConverter\Fixtures;

class DomainObject {
    public function __construct(
        private string $input,
        private int $number,
        private ?string $optional = null
    ) {
    }

    public function getInput(): string
    {
        return $this->input;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function isOptional(): ?string
    {
        return $this->optional;
    }
}