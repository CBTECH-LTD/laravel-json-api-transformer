<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ResourceRelationshipMap implements ResourceRelationshipContract
{
    private string $name;
    private array $items;

    protected function __construct(string $name, array $items)
    {
        Assert::allIsInstanceOf($items, ResourceRelationship::class, 'All items must be an instance of ' . ResourceRelationship::class);

        $this->name = $name;
        $this->items = $items;
    }

    public static function make(string $name, array $items): ResourceRelationshipMap
    {
        return new static($name, $items);
    }

    public static function makeFromTypeMap(string $name, ResourceTypeMap $typeMap): ResourceRelationshipMap
    {
        $items = Collection::make($typeMap->toArray())->map(
            fn(ResourceType $type) => ResourceRelationship::make($name, $type)
        )->all();

        return new static($name, $items);
    }

    public function toArray()
    {
        return [
            $this->getName() => [
                'data' => $this->getData(),
            ],
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): array
    {
        return Collection::make($this->items)
            ->map(fn(ResourceRelationship $rr) => $rr->getData())
            ->all();
    }

    public function toApiResource(): JsonResource
    {
        $items = Collection::make($this->items)
            ->map(fn (ResourceRelationship $rr) => $rr->toApiResource())
            ->all();

        return new ApiResourceCollection($items);
    }
}
