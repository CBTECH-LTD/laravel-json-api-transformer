<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\ApiResources\Traits\WithLinks;
use CbtechLtd\JsonApiTransformer\ApiResources\Traits\WithMeta;
use CbtechLtd\JsonApiTransformer\JsonApiTransformerFacade;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

abstract class ResourceTypeCollection
{
    use WithMeta, WithLinks;

    protected array $items = [];

    abstract public function resourceType(): string;

    public static function make(array $items): self
    {
        return new static($items);
    }

    public function __construct(array $items = [])
    {
        Assert::allIsInstanceOf(
            $items,
            $this->resourceType(),
            'All items must be an instance of ' . $this->resourceType(),
        );

        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function transform(): ApiResourceCollection
    {
        return JsonApiTransformerFacade::collection(Collection::make($this->items))
            ->withLinks($this->getLinks()->all())
            ->withMeta($this->getMeta()->all());
    }
}
