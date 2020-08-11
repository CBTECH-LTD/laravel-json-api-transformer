<?php

namespace CbtechLtd\JsonApiTransformer\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ResourceMetaContract extends Arrayable
{
    public function getName(): string;

    public function getValue();

    public function when(bool $condition): self;
}
