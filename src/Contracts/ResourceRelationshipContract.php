<?php

namespace CbtechLtd\JsonApiTransformer\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;

interface ResourceRelationshipContract extends Arrayable
{
    public function getName(): string;

    public function getData(): array;

    public function toApiResource(): JsonResource;
}
