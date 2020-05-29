<?php

namespace CbtechLtd\JsonApiTransformer\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface ResourceLinkContract extends Arrayable
{
    public function getName(): string;

    public function getValue(): ?string;
}
