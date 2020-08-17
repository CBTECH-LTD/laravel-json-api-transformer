<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

class AnonymousResourceTypeCollection extends ResourceTypeCollection
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function resourceType(): string
    {
        return '';
    }
}
