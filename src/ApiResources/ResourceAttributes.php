<?php

namespace CbtechLtd\JsonApiTransformer\ApiResources;

use Illuminate\Contracts\Support\Arrayable;

class ResourceAttributes implements Arrayable
{
    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->getData();
    }
}
