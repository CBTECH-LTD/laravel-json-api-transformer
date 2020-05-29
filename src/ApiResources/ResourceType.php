<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
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
    abstract public function attributes(Request $request): array;

    public static function single($model): ApiResource
    {
        return app(ApiResourceBuilder::class)->single(static::class, $model);
    }

    public static function collection($models): ApiResourceCollection
    {
        return app(ApiResourceBuilder::class)->collection(static::class, $models);
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
        $this->meta = Collection::make($this->meta());
        return $this->meta;
    }
}
