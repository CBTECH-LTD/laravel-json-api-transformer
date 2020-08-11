<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceMetaContract;

class ResourceMeta implements ResourceMetaContract
{
    protected string $name;
    protected $value;
    protected bool $showCondition = true;

    public static function make(string $name, $value): self
    {
        return new static($name, $value);
    }

    public function __construct(string $name, $value)
    {
        $this->value = $value;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function when(bool $condition): self
    {
        $this->showCondition = $condition;
        return $this;
    }

    public function toArray()
    {
        if (! $this->showCondition) {
            return [];
        }

        return [
            $this->getName() => $this->getValue(),
        ];
    }
}
