<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceMetaContract;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ApiResourceCollection extends ResourceCollection
{
    public $collects = ApiResource::class;
    protected array $links = [];
    protected array $meta = [];

    public static function make(...$parameters)
    {
        return new static($parameters[0]);
    }

    public static function makeFromModels(string $resourceType, Collection $items)
    {
        Assert::isAOf($resourceType, ResourceType::class, 'First parameter must be a ' . ResourceType::class);

        return static::make(
            $items->map(fn($it) => new $resourceType($it))
        );
    }

    public function withMeta(array $meta): self
    {
        Assert::allIsInstanceOf(
            $meta,
            ResourceMetaContract::class,
            'All items must be an instance of ' . ResourceMetaContract::class
        );

        $this->meta = $meta;
        return $this;
    }

    public function withLinks(array $links): self
    {
        Assert::allIsInstanceOf(
            $links,
            ResourceLinkContract::class,
            'All items must be an instance of ' . ResourceLinkContract::class
        );

        $this->links = $links;
        return $this;
    }

    public function toArray($request)
    {
        return [
            'data'  => $this->getData($request),
            'links' => $this->buildLinks(),
            'meta'  => $this->buildMeta(),
        ];
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }

    protected function getData($request): Collection
    {
        return $this->collection->map(
            fn(ApiResource $resource) => $resource->toArray($request)
        );
    }

    protected function buildLinks(): Collection
    {
        return Collection::make($this->links)->mapWithKeys(
            fn(ResourceLinkContract $link) => $link->toArray()
        );
    }

    protected function buildMeta(): Collection
    {
        return Collection::make($this->meta)->mapWithKeys(
            fn(ResourceMetaContract $meta) => $meta->toArray()
        );
    }
}
