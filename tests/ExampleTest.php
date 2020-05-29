<?php

namespace CbtechLtd\JsonApiTransformer\Tests;

use Orchestra\Testbench\TestCase;
use CbtechLtd\JsonApiTransformer\JsonApiTransformerServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [JsonApiTransformerServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
