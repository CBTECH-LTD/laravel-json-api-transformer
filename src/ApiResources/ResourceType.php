<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceMetaContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
use CbtechLtd\JsonApiTransformer\JsonApiTransformerFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

abstract class ResourceType
{
    protected Model $model;
    protected ?Collection $relationships = null;
    protected ?Collection $links = null;
    protected ?Collection $meta = null;
    protected ?Collection $collectionMeta = null;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Determine the type identifier of the resource.
     * Ex: user / product ...
     *
     * @return string
     */
    abstract public function type(): string;

    /**
     * Build an array with all resource attributes.
     *
     * @param Request $request
     * @return array
     */
    public function attributes(Request $request): array
    {
        return $this->getAttributesFromModelFillable();
    }

    public static function single($model): ApiResource
    {
        return JsonApiTransformerFacade::single(static::class, $model);
    }

    public static function collection($models): ApiResourceCollection
    {
        return JsonApiTransformerFacade::collection(static::class, $models);
    }

    /**
     * Get the ID used to fetch the resource.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->model->getRouteKey();
    }

    public function getRelationships(): Collection
    {
        return $this->relationships ?: $this->buildRelationships();
    }

    public function getLinks(): Collection
    {
        return $this->links ?: $this->buildLinks();
    }

    public function getMeta(): Collection
    {
        return $this->meta ?: $this->buildMeta();
    }

    public function getCollectionMeta(): Collection
    {
        return $this->collectionMeta ?: $this->buildCollectionMeta();
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Build an array with all resource relationships.
     *
     * @return array
     */
    protected function relationships(): array
    {
        return [];
    }

    /**
     * Build an array with all resource links.
     *
     * @return array
     */
    protected function links(): array
    {
        return [];
    }

    /**
     * Build an array with metadata related to the resource.
     *
     * @return array
     */
    protected function meta(): array
    {
        return [];
    }

    protected function collectionMeta(): array
    {
        return [];
    }

    protected function buildRelationships(): Collection
    {
        $values = $this->relationships();

        Assert::allIsInstanceOf(
            $values,
            ResourceRelationshipContract::class,
            'All relationships must implement ' . ResourceRelationshipContract::class
        );

        $this->relationships = Collection::make($values)
            ->mapWithKeys(
                fn(ResourceRelationshipContract $relationship) => [$relationship->getName() => $relationship]
            );

        return $this->relationships;
    }

    protected function buildLinks(): Collection
    {
        $values = $this->links();

        Assert::allIsInstanceOf(
            $values,
            ResourceLinkContract::class,
            'All links must implement ' . ResourceLinkContract::class
        );

        $this->links = Collection::make($values)
            ->mapWithKeys(
                fn(ResourceLinkContract $link) => [$link->getName() => $link]
            );

        return $this->links;
    }

    protected function buildMeta(): Collection
    {
        $values = $this->meta();

        Assert::allIsInstanceOf(
            $values,
            ResourceMetaContract::class,
            'All meta must implement ' . ResourceMetaContract::class,
        );

        $this->meta = Collection::make($values)
            ->mapWithKeys(
                fn(ResourceMetaContract $meta) => [$meta->getName() => $meta],
            );

        return $this->meta;
    }

    protected function buildCollectionMeta(): Collection
    {
        $this->collectionMeta = Collection::make($this->collectionMeta());
        return $this->collectionMeta;
    }

    protected function getAttributesFromModelFillable(): array
    {
        $fields = $this->model->getFillable();

        return Collection::make($fields)
            ->mapWithKeys(
                fn(string $field) => [$field => $this->model->{$field}]
            )->all();
    }
}
