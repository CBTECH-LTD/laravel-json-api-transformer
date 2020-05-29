<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class ResourceRelationship implements ResourceRelationshipContract
{
    private string $name;
    private ResourceType $resourceType;

    protected function __construct(string $name, ResourceType $resourceType)
    {
        $this->name = $name;
        $this->resourceType = $resourceType;
    }

    public static function make(string $name, ResourceType $resourceType): ResourceRelationship
    {
        return new static($name, $resourceType);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toApiResource(): JsonResource
    {
        return new ApiResource($this->resourceType);
    }

    public function getData(): array
    {
        return [
            'type' => $this->resourceType->type(),
            'id'   => $this->resourceType->id(),
        ];
    }

    public function getLinks(): array
    {
        $links = [];

        if ($self = Collection::make($this->resourceType->getLinks())->first(fn(ResourceLink $rl) => $rl->getName() === 'self')) {
            $links['related'] = $self;
        }

        return $links;
    }

    public function toArray()
    {
        return [
            $this->getName() => [
                'data'  => $this->getData(),
                'links' => $this->buildLinks(),
            ],
        ];
    }

    protected function buildLinks(): array
    {
        $values = $this->getLinks();

        Assert::allIsInstanceOf(
            $values,
            ResourceLinkContract::class,
            'All links must implement ' . ResourceLinkContract::class
        );

        return Collection::make($values)
            ->mapWithKeys(
                fn(ResourceLinkContract $link) => $link->toArray()
            )->all();
    }
}
