<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ResourceTypeMap implements Arrayable
{
    protected array $items;

    public function __construct(string $resourceTypeClass, array $items)
    {
        Assert::isAOf($resourceTypeClass, ResourceType::class, 'First parameter must be a instance of ' . ResourceType::class);

        $this->items = Collection::make($items)->map(
            fn($item) => new $resourceTypeClass($item)
        )->all();
    }

    public static function make(...$parameters): ResourceTypeMap
    {
        return new static(...$parameters);
    }

    public function toArray()
    {
        return $this->items;
    }
}
