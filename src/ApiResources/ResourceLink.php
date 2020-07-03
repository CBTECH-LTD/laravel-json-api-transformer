<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use Illuminate\Support\Facades\URL;

class ResourceLink implements ResourceLinkContract
{
    private string $name;
    private array $route;
    private bool $showCondition = true;

    public function __construct(string $name, array $route)
    {
        $this->name = $name;
        $this->route = $route;
    }

    public static function make(string $name, array $route)
    {
        return new static($name, $route);
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): ?string
    {
        if (! $this->showCondition) {
            return null;
        }

        return URL::relative(...$this->getRoute());
    }

    public function getRoute(): array
    {
        return $this->route;
    }

    public function when(bool $condition): self
    {
        $this->showCondition = $condition;
        return $this;
    }
}
