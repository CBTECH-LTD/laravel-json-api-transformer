<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\ApiResources\Traits\WithLinks;
use CbtechLtd\JsonApiTransformer\ApiResources\Traits\WithMeta;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
use CbtechLtd\JsonApiTransformer\JsonApiTransformerFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

abstract class ResourceType
{
    use WithMeta, WithLinks;

    protected Model $model;
    protected ?Collection $relationships = null;
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

    public static function transformToSingle($model): ApiResource
    {
        return JsonApiTransformerFacade::single(static::class, $model);
    }

    public static function transformToCollection($models): ApiResourceCollection
    {
        return JsonApiTransformerFacade::collectionFromModels(static::class, $models);
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
