<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ApiResourceCollection extends ResourceCollection
{
    public $collects = ApiResource::class;

    public static function make(...$parameters)
    {
        return static::makeWithType($parameters[0], $parameters[1]);
    }

    public static function makeWithType(string $resourceType, Collection $items)
    {
        Assert::isAOf($resourceType, ResourceType::class, 'First parameter must be a ' . ResourceType::class);

        return new static(
            $items->map(fn($it) => new $resourceType($it))
        );
    }

    public function toArray($request)
    {
        return [
            'data'  => $this->getData($request),

            // TODO: Build the links object.
            'links' => [],

            // TODO: Build the meta object.
            'meta'  => [],
        ];
    }

    protected function getData($request): Collection
    {
        return $this->collection->map(
            fn(ApiResource $resource) => $resource->toArray($request)
        );
    }
}
