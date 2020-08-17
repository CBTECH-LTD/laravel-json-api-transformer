<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources\Traits;

use CbtechLtd\JsonApiTransformer\Contracts\ResourceLinkContract;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

trait WithLinks
{
    protected ?Collection $compiledLinks = null;
    protected array $additionalLinks = [];

    public function withLinks(array $links): self
    {
        Assert::allIsInstanceOf(
            $links,
            ResourceLinkContract::class,
            'All items must implement ' . ResourceLinkContract::class,
        );

        $this->additionalLinks = $links;
        return $this;
    }

    public function getLinks(): Collection
    {
        return $this->compiledLinks ?: $this->buildLinks();
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

    protected function buildLinks(): Collection
    {
        $values = array_merge($this->links(), $this->additionalLinks);

        Assert::allIsInstanceOf(
            $values,
            ResourceLinkContract::class,
            'All links must implement ' . ResourceLinkContract::class
        );

        $this->compiledLinks = Collection::make($values)
            ->mapWithKeys(
                fn(ResourceLinkContract $link) => [$link->getName() => $link]
            );

        return $this->compiledLinks;
    }
}
