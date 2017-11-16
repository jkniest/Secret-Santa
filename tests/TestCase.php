<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function create(string $class, array $attributes = [], int $amount = null)
    {
        return factory($class, $amount)->create($attributes);
    }
}
