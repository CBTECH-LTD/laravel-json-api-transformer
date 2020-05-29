<?php

namespace CbtechLtd\JsonApiTransformer;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CbtechLtd\JsonApiTransformer\JsonApiTransformer
 */
class JsonApiTransformerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-json-api-resources';
    }
}
