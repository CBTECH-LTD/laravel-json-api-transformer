<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources\Traits;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceMetaContract;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

trait WithMeta
{
    protected ?Collection $compiledMeta = null;
    protected array $additionalMeta = [];

    public function withMeta(array $meta): self
    {
        Assert::allIsInstanceOf(
            $meta,
            ResourceMetaContract::class,
            'All items must implement ' . ResourceMetaContract::class,
        );

        $this->additionalMeta = array_merge($this->additionalMeta, $meta);
        return $this;
    }

    /**
     * Get built meta.
     *
     * @return Collection
     */
    public function getMeta(): Collection
    {
        return $this->compiledMeta ?: $this->buildMeta();
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

    /**
     * Build the meta array.
     *
     * @return Collection
     */
    protected function buildMeta(): Collection
    {
        $values = array_merge($this->meta(), $this->additionalMeta);

        Assert::allIsInstanceOf(
            $values,
            ResourceMetaContract::class,
            'All meta must implement ' . ResourceMetaContract::class,
        );

        $this->compiledMeta = Collection::make($values)
            ->mapWithKeys(
                fn(ResourceMetaContract $meta) => [$meta->getName() => $meta],
            );

        return $this->compiledMeta;
    }
}
