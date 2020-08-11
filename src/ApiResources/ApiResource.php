<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceMetaContract;
use CbtechLtd\JsonApiTransformer\Contracts\ResourceRelationshipContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class SingleResource
 *
 * @property ResourceType $resource
 * @package CbtechLtd\JsonApiTransformer\ApiResources
 */
class ApiResource extends JsonResource
{
    public static $wrap = null;
    protected $included = [];

    public function getModel(): Model
    {
        return $this->resource->getModel();
    }

    public function includes(...$relationshipsToInclude): self
    {
        $this->included = $relationshipsToInclude;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \ReflectionException
     */
    public function toArray($request)
    {
        return [
            'type'          => $this->resource->type(),
            'id'            => $this->resource->id(),
            'attributes'    => $this->resource->attributes($request),
            'relationships' => $this->buildRelationships(),
            'links'         => $this->buildLinks(),
            'included'      => $this->buildIncluded($request),
            'meta'          => $this->buildMeta(),
        ];
    }

    protected function buildIncluded(Request $request): array
    {
        $includeFromRequest = explode(',', $request->input('include', ''));

        return Collection::make(array_merge($includeFromRequest, $this->included))
            ->filter()
            ->mapWithKeys(function ($include) use ($request) {
                /** @var ResourceRelationshipContract $relationship */
                if (! $relationship = $this->resource->getRelationships()->get($include)) {
                    throw new \Exception("Included field [{$include}] is not a valid relationship");
                }

                return [
                    $include => $relationship->toApiResource(),
                ];
            })->all();
    }

    protected function buildRelationships(): Collection
    {
        return $this->resource->getRelationships()->mapWithKeys(
            fn(ResourceRelationshipContract $relationship) => $relationship->toArray()
        );
    }

    /**
     * @return Collection
     */
    protected function buildLinks(): Collection
    {
        return $this->resource->getLinks()->mapWithKeys(
            fn(ResourceLinkContract $link) => $link->toArray()
        );
    }

    protected function buildMeta(): Collection
    {
        return $this->resource->getMeta()->mapWithKeys(
            fn(ResourceMetaContract $meta) => $meta->toArray()
        );
    }
}
